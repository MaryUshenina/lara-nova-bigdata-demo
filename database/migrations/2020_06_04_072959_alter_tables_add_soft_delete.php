<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablesAddSoftDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users',function (Blueprint $table){
            $table->softDeletes();
        });

        Schema::table('ads',function (Blueprint $table){
            $table->softDeletes();
        });

        Schema::table('photos',function (Blueprint $table){
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('users',function (Blueprint $table){
            $table->dropSoftDeletes();
        });

        Schema::table('ads',function (Blueprint $table){
            $table->dropSoftDeletes();
        });

        Schema::table('photos',function (Blueprint $table){
            $table->dropSoftDeletes();
        });

    }
}
