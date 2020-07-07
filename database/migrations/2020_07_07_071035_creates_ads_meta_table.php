<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\Artisan;

class CreatesAdsMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads_meta', function (Blueprint $table) {
            $table->unsignedMediumInteger('ad_id');
            $table->unsignedSmallInteger('user_id')->index();
            $table->string('country', 2)->index();
            $table->mediumInteger('created_at_ymd')->index();
            $table->mediumInteger('end_date_ymd')->index();
            $table->float('price');
            $table->smallInteger('price_group')->index();


            $table->index(['price_group', 'country', 'user_id', 'created_at_ymd']);
            $table->index(['price_group', 'country', 'user_id']);
            $table->index(['price_group', 'country', 'created_at_ymd']);
            $table->index(['price_group', 'user_id', 'created_at_ymd']);

            $table->index(['price_group', 'country']);
            $table->index(['price_group', 'user_id']);
            $table->index(['country', 'user_id']);

            $table->index(['price_group', 'created_at_ymd']);
            $table->index(['country', 'created_at_ymd']);

            $table->index(['user_id', 'created_at_ymd']);

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ads_meta');
    }
}
