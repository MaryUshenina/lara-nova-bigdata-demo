<?php

namespace App\Jobs;

use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $parent = Category::find($this->parent_id);
        $child = Category::find($this->child_id);

        $child->parentCategories()->wherePivot('level', '<>', 0)->detach();

        //inherit tree from all parents to current child
        $all_parents = [];
        if ($parent) {
            foreach ($parent->parentCategories()->get() as $subParentCategory) {
                if ($subParentCategory->id == $child->id) {
                    continue;
                }

                $newLevel = $subParentCategory->pivot->level + 1;
                $child->parentCategories()->attach($subParentCategory, ['level' => $newLevel]);

                $all_parents[$subParentCategory->id] = $newLevel;
            }
        }
        // draw children
        $this->drawForChildren($child, $all_parents, 1);
    }

    /**
     * inherit to children
     *
     * @param $child
     * @param array $all_parents
     * @param int $i
     */
    private function drawForChildren($child, $all_parents = [], $i = 1)
    {
        foreach ($all_parents as $parent_id => $level) {
            $all_parents[$parent_id] = $level + 1;
        }
        $all_parents[$child->id] = 1;

        foreach ($child->childrenCategories()->get() as $subChild) {
            $subChild->parentCategories()->wherePivot('level', '<>', 0)->detach();

            foreach ($all_parents as $parent_id => $level) {
                if ($subChild->id == $parent_id) {
                    continue;
                }

                $subChild->parentCategories()->attach($parent_id, ['level' => $level]);
            }
            $this->drawForChildren($subChild, $all_parents, $i + 1);
        }

    }
}
