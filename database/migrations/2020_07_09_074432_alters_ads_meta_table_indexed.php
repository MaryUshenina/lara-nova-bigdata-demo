<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use \Illuminate\Support\Facades\DB;
use \App\Models\Ad;

class AltersAdsMetaTableIndexed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //delete duplicated
        $duplicates = DB::Table('ads_meta')
            ->groupBy('ad_id')
            ->havingRaw('COUNT(*)>1')
            ->pluck('ad_id');

        DB::Table('ads_meta')->whereIn('ad_id', $duplicates)->delete();

        foreach ($duplicates as $duplicateId) {
            Ad::find($duplicateId)->updateMetaData();
        }


        Schema::table('ads_meta', function (Blueprint $table) {
            $table->unique(['ad_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ads_meta', function (Blueprint $table) {
            $table->dropUnique(['ad_id']);
        });
    }
}
