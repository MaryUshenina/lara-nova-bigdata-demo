<?php

use App\Models\CalendarPrice;
use Illuminate\Database\Seeder;
use \App\Models\Ad;
use \App\Models\User;
use \App\Models\Category;
use \Illuminate\Support\Facades\Hash;

class AdsSeed extends Seeder
{
    const ADMIN_EMAIL = 'admin@testapp.com';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::firstOrCreate([
            'email' => self::ADMIN_EMAIL,
            'role' => User::ADMIN_USER_ROLE
        ], [
            'name' => 'admin',
            'password' => Hash::make('admin'),
            'email_verified_at' => now(),
        ]);

        DB::statement("SET FOREIGN_KEY_CHECKS=0;");
        DB::statement("TRUNCATE TABLE `ads`; ");
        DB::statement("TRUNCATE TABLE `ads_category`; ");
        DB::statement("SET FOREIGN_KEY_CHECKS=1;");

        $count = 3000000;
        $part = 100;

        $this->command->getOutput()->progressStart($count);
        $allCategories = Category::with('parentCategories')->get()->keyBy('id');

        $allCategoriesKeys = $allCategories->keys()->toArray();

        for ($i = 0; $i < $count; $i += $part) {
            $ads_category_data = [];
            $user = User::where('role', User::ESTATE_USER_ROLE)->get()->random(1)->first();

            factory(Ad::class, $part)->create([
                'user_id' => $user->id
            ])
                ->each(function ($ad) use ($allCategories, $allCategoriesKeys, &$ads_category_data) {
                    $count = mt_rand(0, 3);
                    if (!$count) {
                        return;
                    }

                    $categoryIds = array_rand($allCategoriesKeys, $count);
                    if (is_int($categoryIds)) {
                        $categoryIds = [$categoryIds];
                    }

                    foreach ($categoryIds as $categoryId) {

                        if (!isset($allCategories[$categoryId])) {
                            continue;
                        }

                        $parentCategories = $allCategories[$categoryId]->parentCategories->pluck('id')->toArray() ?? [];
                        foreach ($parentCategories as $id) {
                            $ads_category_data[] = ['category_id' => $id, 'ad_id' => $ad->id];
                        }
                    }
                });

            DB::table('ads_category')->insertOrIgnore($ads_category_data);

            $this->command->getOutput()->progressAdvance($part);
        }
        $this->command->getOutput()->progressFinish();
    }
}
