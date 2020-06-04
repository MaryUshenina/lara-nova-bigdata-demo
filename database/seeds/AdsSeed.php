<?php

use Illuminate\Database\Seeder;
use \App\Models\Ad;

class AdsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i <= 20; $i++) {
            factory(Ad::class)->create();
        }
    }
}
