<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdTableChangeCountry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads',function (Blueprint $table){
            $table->dropColumn('country_id');
            $table->char('country', 2)->nullable();
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
            $table->dropColumn('country');
            $table->unsignedSmallInteger('country_id');
        });
    }
}
