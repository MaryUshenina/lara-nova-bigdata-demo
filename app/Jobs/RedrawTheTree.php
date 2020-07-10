<?php

namespace App\Jobs;

use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RedrawTheTree implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $child_id;
    private $parent_id;

    /**
     * Create a new job instance.
     *
     * RedrawTheTree constructor.
     * @param int $child_id
     * @param int $parent_id
     */
    public function __construct(int $child_id, int $parent_id)
    {
        $this->child_id = $child_id;
        $this->parent_id = $parent_id;

    }

    /**
     * # ancestor - parent_id
     * # descendant - child_id
     */
    public function handle()
    {
        $child = Category::find($this->child_id);
        $originalPid = $child->parentCategories()->where('level', 1)->first()->id ?? 0;

        if ($originalPid != 0) {

            // unlink node
            Db::statement("delete from categories_tree
                where child_id in (
                    select child_id from (
                        select child_id from categories_tree where parent_id=$this->child_id
                    ) as t1
                )and parent_id in (
                    select parent_id from (
                        select parent_id from categories_tree where child_id = $this->child_id
                        and  parent_id != child_id
                    ) as t2
                )");


            // link orphan node
            Db::statement("insert into categories_tree(parent_id, child_id, level)
                select supertree.parent_id, subtree.child_id, subtree.level+1
                from categories_tree as supertree
                cross join categories_tree as subtree
                where supertree.child_id = $this->parent_id
                    and subtree.parent_id = $this->child_id");

        } elseif ($childsCount = $child->childrenCategories->count()) {

            // if child is not leaf but is located on root level
            Db::statement("insert into categories_tree(parent_id, child_id, level)
                select supertree.parent_id, subtree.child_id, subtree.level+supertree.level+1
                from categories_tree as supertree
                cross join categories_tree as subtree
                where supertree.child_id = $this->parent_id
                    and subtree.parent_id = $this->child_id");

        } else {
            //moving leaf
            Db::statement("insert into categories_tree(parent_id, child_id, level)
                    select t.parent_id, $this->child_id, t.level+1
                    from categories_tree as t
                    where t.child_id = $this->parent_id");
        }

        // handle ads
        $this->moveAdsToNewCategory($child, $originalPid);

        dispatch(new GenerateMetricsCache());
    }

    /**
     * @param Category $child
     * @param Category $parent
     * @param $originalPid
     */
    private function moveAdsToNewCategory(Category $child, $originalPid)
    {
        $adIds = DB::table('ads_categories')->where('category_id', $child->id)->pluck('ad_id');
        if (!count($adIds)) {
            return;  // no ads are attached
        }

        // detach old pid from all ads
        if ($originalPid > 0) {
            \DB::table('ads_categories')->where('category_id', $originalPid)
                ->whereIn('ad_id', $adIds)
                ->delete();
        }

        if (!$this->parent_id) {
            return; // new parent is root
        }

        $nestedParents = DB::table('categories_tree')->where('child_id', $this->parent_id)->pluck('parent_id');

        // attach new parents tree
        $adsCategoriesData = [];
        foreach ($adIds as $adId) {
            $adsCategoriesData[] = [
                'ad_id' => $adId,
                'category_id' => $this->parent_id,
            ];

            foreach ($nestedParents ?? [] as $nestedParent) {
                $adsCategoriesData[] = [
                    'ad_id' => $adId,
                    'category_id' => $nestedParent,
                ];
            }
        }

        \DB::table('ads_categories')->insertOrIgnore($adsCategoriesData);
    }

}
