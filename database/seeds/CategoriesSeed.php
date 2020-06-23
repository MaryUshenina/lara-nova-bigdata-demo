<?php

use Illuminate\Database\Seeder;

use \Illuminate\Support\Facades\DB;

use \App\Models\Category;
use \App\Models\EagerCategory;

//use \App\Models\CategoryTree;

use Illuminate\Support\Facades\Schema;

class CategoriesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0;");
        DB::statement("TRUNCATE TABLE `categories_tree`; ");
        DB::statement("TRUNCATE TABLE `categories`; ");
        DB::statement("SET FOREIGN_KEY_CHECKS=1;");

        $coef = 15;
        $allowed = 1000;
        $maxNestingLevel = 5;
        $countOnLevel0 = 50;

        //level 0
        $this->factoryLevel(0, $countOnLevel0);
        $allowed -= $countOnLevel0;

        //other levels
        for ($level = 1; $level <= $maxNestingLevel; $level++) {
            $countOnLevel = pow($level + 1, 2) * $coef;
            if ($allowed < $countOnLevel) {
                $countOnLevel = $allowed;
            }

            $this->factoryLevel($level, $countOnLevel);

            $allowed -= $countOnLevel;
            if ($allowed <= 0) {
                break;
            }
        }
    }

    private function factoryLevel($level, $countOnLevel)
    {
        factory(Category::class, $countOnLevel)->create()
            ->each(function ($category) use ($level) {

                if (!$level) {
                    return;
                }
                $randomParent = EagerCategory::where('max_level', '=', $level - 1)->get()->random(1)->first();
                if (!$randomParent) {
                    $randomParent = EagerCategory::first();
                }

                $category->pid = $randomParent->id;
                $category->save();
            });
    }
}
