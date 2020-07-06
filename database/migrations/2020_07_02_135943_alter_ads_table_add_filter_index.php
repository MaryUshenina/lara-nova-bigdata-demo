<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdsTableAddFilterIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads',function (Blueprint $table){
            $table->index('country', 'country');
            $table->index('price', 'price');
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
            $table->dropIndex('country');
            $table->dropIndex('price');
        });
    }
}
