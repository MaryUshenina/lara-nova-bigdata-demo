<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdsTableAddCoordinatesColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads',function (Blueprint $table){
            $table->float('location_lat', 10, 6)->nullable();
            $table->float('location_lng', 10, 6)->nullable();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ads',function (Blueprint $table){
            $table->dropColumn('location_lat', 'location_lng');
        });
    }
}
