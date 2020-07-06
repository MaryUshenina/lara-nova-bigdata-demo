<?php

use Illuminate\Database\Seeder;
use \App\Models\Ad;
use \App\Models\User;
use \App\Models\Category;
use \Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdsSeed extends Seeder
{
    const ADMIN_EMAIL = 'admin@testapp.com';

    private $countriesList;
    private $usersList;
    private $faker;
    private $allCategories;
    private $allCategoriesKeys;

    private $adsWithNoCategories;

    public function __construct()
    {
        $this->countriesList = array_flip(\Countries::getList('en'));
        $this->usersList = User::where('role', User::ESTATE_USER_ROLE)->pluck('id');
        $this->faker = Faker\Factory::create();

        $this->allCategories = Category::with('parentCategories')->get()->keyBy('id');
        $this->allCategoriesKeys = $this->allCategories->keys()->toArray();
    }

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

        $count = 3000000;
        $part = 1000;

        $this->command->getOutput()->progressStart($count);

        // already exists
        $countExists = Ad::count();
        $this->command->getOutput()->progressAdvance($countExists);

        for ($i = $countExists; $i < $count; $i += $part) {
            $adsData = [];

            for ($j = 0; $j < $part; $j++) {
                $adsData[] = $this->getAdFactoryArray();
            }

            DB::table('ads')->insertOrIgnore($adsData);
            $this->command->getOutput()->progressAdvance($part);
        }
        $this->command->getOutput()->progressFinish();

        $this->addCategoriesToAds();
    }

    /**
     * attach 0-3 categories to ads
     */
    private function addCategoriesToAds()
    {
        $adsIdsCollection = collect(Db::select("SELECT ads.id FROM ads
                    LEFT JOIN ads_categories ON ads.id = ads_categories.ad_id
                    GROUP BY ads.id
                    HAVING COUNT(ads_categories.category_id) = 0
                    LIMIT 1000"
            . ($this->adsWithNoCategories ? " offset $this->adsWithNoCategories" : '')));

        if (!$count = $adsIdsCollection->count()) {
            return;
        }
        $this->command->getOutput()->progressStart($count);

        $adsCategoriesData = [];
        foreach ($adsIdsCollection as $ad) {
            $countOfAttachingCategories = mt_rand(0, 3);
            if (!$countOfAttachingCategories) {
                $this->adsWithNoCategories++;
                continue;
            }
            $categoryIds = array_rand($this->allCategoriesKeys, $countOfAttachingCategories);
            if (is_int($categoryIds)) {
                $categoryIds = [$categoryIds];
            }

            foreach ($categoryIds as $categoryId) {
                if (!isset($this->allCategories[$categoryId])) {
                    continue;
                }

                $parentCategories = $this->allCategories[$categoryId]->parentCategories->pluck('id')->toArray() ?? [];
                foreach ($parentCategories as $id) {
                    $adsCategoriesData[] = ['category_id' => $id, 'ad_id' => $ad->id];
                }
            }
            $this->command->getOutput()->progressAdvance(1);
        };

        DB::table('ads_categories')->insertOrIgnore($adsCategoriesData);
        $this->command->getOutput()->progressFinish();

        // repeat for next part
        $this->addCategoriesToAds();
    }

    /**
     * @return array
     */
    private function getAdFactoryArray()
    {
        return [
            'user_id' => $this->faker->randomElement($this->usersList),
            'title' => $this->faker->sentence,
            'description' => $this->faker->text(1000),
            'phone' => '+1 ' . $this->faker->numerify('(###) ###-####'),
            'country' => $this->faker->randomElement($this->countriesList),
            'price' => $this->faker->randomFloat(2, 0, 99999.99),
            'email' => $this->faker->email,
            'end_date' => $this->faker->dateTimeBetween('-1 month', '+3 months'),
            'created_at_date' => $this->faker->dateTimeBetween('-3 month')
        ];
    }
}
