<?php

namespace App\Console\Commands;

use App\Models\Ad;
use App\Models\Category;
use App\Models\CompiledTreeCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestTree extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:tree';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test the category tree';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Schema::disableForeignKeyConstraints();

        DB::table('categories_tree')->truncate();
        DB::table('categories')->truncate();
        DB::table('ads_categories')->truncate();
        DB::table('ads')->truncate();
        DB::table('ads_meta')->truncate();

        Schema::enableForeignKeyConstraints();

        $this->testInitial();

        // test on moving
        $this->test2to13();
        $this->test2to1();

        $this->test2to0();
        $this->test2to9();

        $this->test2to1();
    }

    private function draw($pid, $data)
    {
        foreach ($data as $name => $children) {
            $item = Category::firstOrCreate(['name' => $name]);

            if ($pid > 0) {
                $item->pid = $pid;
                $item->save();
            }
            if (count($children)) {
                $this->draw($item->id, $children);
            }
        }

    }

    private function testIsEqualData($data)
    {
        $items = CompiledTreeCategory::all()->keyBy('id');
        foreach ($data as $id => $row) {

            if (!isset($items[$id])) {
                $this->error("item $id not found");
                continue;
            }

            $item = $items[$id];
            foreach ($row as $field => $value) {
                if ($item->$field <> $value) {
                    $this->info("ERROR [$field] found:{$item->$field} <> must:$value");
                    continue;
                }
            }

        }
    }

    private function testAdsCategories($data)
    {
        $adsCategoryData = DB::table('ads_categories')
            ->select([
                'ad_id',
                DB::raw("GROUP_CONCAT(LPAD(category_id, 4, 0)) AS 'tree_order' ")
            ])
            ->groupBy('ad_id')
            ->get()
            ->keyBy('ad_id');

        foreach ($adsCategoryData as $i => $row) {
            $temp1 = explode(',', $row->tree_order);
            $temp2 = explode(',', $data[$i]['tree_order']);
            $dif1 = array_diff($temp1, $temp2);
            $dif2 = array_diff($temp2, $temp1);
            if (count($dif1) || count($dif2)) {
                $this->info("ERROR [ads_category_data $i] found:$row->tree_order <> must:{$data[$i]['tree_order']}");
            }
        }
    }

    private function testInitial()
    {

        $this->info("testInitial");
        $data = [
            111 => [
                '111.1' => [
                    '111.1.1' => [
                        '111.1.1.1' => [],
                        '111.1.1.2' => [],
                    ],
                    '111.1.2' => [
                        '111.1.2.1' => [],
                        '111.1.2.2' => [],
                    ]
                ],
                '111.2' => [
                    '111.2.1' => [
                        '111.2.1.1' => [],
                        '111.2.1.2' => [],
                    ],
                ]
            ],

            222 => [],
            333 => [],
            444 => []
        ];

        $this->draw(0, $data);

        $testData = [
            1 => ['name' => '111', 'tree_order' => '0001'],
            2 => ['name' => '111.1', 'tree_order' => '0001,0002'],
            3 => ['name' => '111.1.1', 'tree_order' => '0001,0002,0003'],
            4 => ['name' => '111.1.1.1', 'tree_order' => '0001,0002,0003,0004'],
            5 => ['name' => '111.1.1.2', 'tree_order' => '0001,0002,0003,0005'],
            6 => ['name' => '111.1.2', 'tree_order' => '0001,0002,0006'],
            7 => ['name' => '111.1.2.1', 'tree_order' => '0001,0002,0006,0007'],
            8 => ['name' => '111.1.2.2', 'tree_order' => '0001,0002,0006,0008'],
            9 => ['name' => '111.2', 'tree_order' => '0001,0009'],
            10 => ['name' => '111.2.1', 'tree_order' => '0001,0009,0010'],
            11 => ['name' => '111.2.1.1', 'tree_order' => '0001,0009,0010,0011'],
            12 => ['name' => '111.2.1.2', 'tree_order' => '0001,0009,0010,0012'],
            13 => ['name' => '222', 'tree_order' => '0013'],
            14 => ['name' => '333', 'tree_order' => '0014'],
            15 => ['name' => '444', 'tree_order' => '0015'],
        ];
        $this->testIsEqualData($testData);


        //add ads
        for ($i = 1; $i <= 15; $i++) {
            $ad = factory(Ad::class)->create([
                'title' => "testAd#$i"
            ]);
            $ad->categories()->attach([$i]);
        }

        $this->testAdsCategories($testData);
    }

    private function test2to13()
    {
        $this->info("\ntest2to13");
        $item = Category::find(2);
        $item->pid = 13;
        $item->save();
        $testData = [
            1 => ['name' => '111', 'tree_order' => '0001'],

            9 => ['name' => '111.2', 'tree_order' => '0001,0009'],
            10 => ['name' => '111.2.1', 'tree_order' => '0001,0009,0010'],
            11 => ['name' => '111.2.1.1', 'tree_order' => '0001,0009,0010,0011'],
            12 => ['name' => '111.2.1.2', 'tree_order' => '0001,0009,0010,0012'],

            13 => ['name' => '222', 'tree_order' => '0013'],

            2 => ['name' => '111.1', 'tree_order' => '0013,0002'],
            3 => ['name' => '111.1.1', 'tree_order' => '0013,0002,0003'],
            4 => ['name' => '111.1.1.1', 'tree_order' => '0013,0002,0003,0004'],
            5 => ['name' => '111.1.1.2', 'tree_order' => '0013,0002,0003,0005'],
            6 => ['name' => '111.1.2', 'tree_order' => '0013,0002,0006'],
            7 => ['name' => '111.1.2.1', 'tree_order' => '0013,0002,0006,0007'],
            8 => ['name' => '111.1.2.2', 'tree_order' => '0013,0002,0006,0008'],


            14 => ['name' => '333', 'tree_order' => '0014'],
            15 => ['name' => '444', 'tree_order' => '0015'],
        ];
        $this->testIsEqualData($testData);
        $this->testAdsCategories($testData);
    }

    private function test2to1()
    {
        $this->info("\ntest2to1");
        $item = Category::find(2);
        $item->pid = 1;
        $item->save();

        $testData = [
            1 => ['name' => '111', 'tree_order' => '0001'],
            2 => ['name' => '111.1', 'tree_order' => '0001,0002'],
            3 => ['name' => '111.1.1', 'tree_order' => '0001,0002,0003'],
            4 => ['name' => '111.1.1.1', 'tree_order' => '0001,0002,0003,0004'],
            5 => ['name' => '111.1.1.2', 'tree_order' => '0001,0002,0003,0005'],
            6 => ['name' => '111.1.2', 'tree_order' => '0001,0002,0006'],
            7 => ['name' => '111.1.2.1', 'tree_order' => '0001,0002,0006,0007'],
            8 => ['name' => '111.1.2.2', 'tree_order' => '0001,0002,0006,0008'],
            9 => ['name' => '111.2', 'tree_order' => '0001,0009'],
            10 => ['name' => '111.2.1', 'tree_order' => '0001,0009,0010'],
            11 => ['name' => '111.2.1.1', 'tree_order' => '0001,0009,0010,0011'],
            12 => ['name' => '111.2.1.2', 'tree_order' => '0001,0009,0010,0012'],
            13 => ['name' => '222', 'tree_order' => '0013'],
            14 => ['name' => '333', 'tree_order' => '0014'],
            15 => ['name' => '444', 'tree_order' => '0015'],
        ];

        $this->testIsEqualData($testData);
        $this->testAdsCategories($testData);
    }


    private function test2to0()
    {
        $this->info("\ntest2to0");
        $item = Category::find(2);
        $item->pid = 0;
        $item->save();

        $testData = [
            1 => ['name' => '111', 'tree_order' => '0001'],


            9 => ['name' => '111.2', 'tree_order' => '0001,0009'],
            10 => ['name' => '111.2.1', 'tree_order' => '0001,0009,0010'],
            11 => ['name' => '111.2.1.1', 'tree_order' => '0001,0009,0010,0011'],
            12 => ['name' => '111.2.1.2', 'tree_order' => '0001,0009,0010,0012'],

            2 => ['name' => '111.1', 'tree_order' => '0002'],
            3 => ['name' => '111.1.1', 'tree_order' => '0002,0003'],
            4 => ['name' => '111.1.1.1', 'tree_order' => '0002,0003,0004'],
            5 => ['name' => '111.1.1.2', 'tree_order' => '0002,0003,0005'],
            6 => ['name' => '111.1.2', 'tree_order' => '0002,0006'],
            7 => ['name' => '111.1.2.1', 'tree_order' => '0002,0006,0007'],
            8 => ['name' => '111.1.2.2', 'tree_order' => '0002,0006,0008'],

            13 => ['name' => '222', 'tree_order' => '0013'],
            14 => ['name' => '333', 'tree_order' => '0014'],
            15 => ['name' => '444', 'tree_order' => '0015'],
        ];

        $this->testIsEqualData($testData);
        $this->testAdsCategories($testData);
    }


    private function test2to9()
    {
        $this->info("\ntest2to9");
        $item = Category::find(2);
        $item->pid = 9;
        $item->save();

        $testData = [
            1 => ['name' => '111', 'tree_order' => '0001'],

            9 => ['name' => '111.2', 'tree_order' => '0001,0009'],

            2 => ['name' => '111.1', 'tree_order' => '0001,0009,0002'],
            3 => ['name' => '111.1.1', 'tree_order' => '0001,0009,0002,0003'],
            4 => ['name' => '111.1.1.1', 'tree_order' => '0001,0009,0002,0003,0004'],
            5 => ['name' => '111.1.1.2', 'tree_order' => '0001,0009,0002,0003,0005'],
            6 => ['name' => '111.1.2', 'tree_order' => '0001,0009,0002,0006'],
            7 => ['name' => '111.1.2.1', 'tree_order' => '0001,0009,0002,0006,0007'],
            8 => ['name' => '111.1.2.2', 'tree_order' => '0001,0009,0002,0006,0008'],

            10 => ['name' => '111.2.1', 'tree_order' => '0001,0009,0010'],
            11 => ['name' => '111.2.1.1', 'tree_order' => '0001,0009,0010,0011'],
            12 => ['name' => '111.2.1.2', 'tree_order' => '0001,0009,0010,0012'],
            13 => ['name' => '222', 'tree_order' => '0013'],
            14 => ['name' => '333', 'tree_order' => '0014'],
            15 => ['name' => '444', 'tree_order' => '0015'],
        ];

        $this->testIsEqualData($testData);
        $this->testAdsCategories($testData);
    }


}
