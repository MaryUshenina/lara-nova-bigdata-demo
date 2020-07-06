<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads_category', function (Blueprint $table) {

            $table->unsignedBigInteger('ad_id');
            $table->unsignedBigInteger('category_id');

            $table->foreign('ad_id')
                ->references('id')
                ->on('ads');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories');

            $table->unique(['ad_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ads_category');
    }
}
