<?php

use Illuminate\Database\Seeder;

use \App\Models\Category;

class CategoriesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allowed = 1000;
        $maxNestingLevel = 5;
        for ($i = 1; $i <= $maxNestingLevel+1; $i++) {
            $countOnLevel = pow($i,2) * 15;
            if ($allowed < $countOnLevel) {
                $countOnLevel = $allowed;
            }

            factory(Category::class, $countOnLevel)->create();
            $allowed -= $countOnLevel;
        }
    }
}
