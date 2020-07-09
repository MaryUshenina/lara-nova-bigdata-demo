<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AltersAdsMetaTableDropIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads_meta', function (Blueprint $table) {

            //delete old
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at_ymd']);
            $table->dropIndex(['end_date_ymd']);
            $table->dropIndex(['price_group']);
            $table->dropIndex(['country']);


            $table->dropIndex(['price_group', 'country', 'user_id', 'created_at_ymd']);
            $table->dropIndex(['price_group', 'country', 'user_id']);
            $table->dropIndex(['price_group', 'country', 'created_at_ymd']);
            $table->dropIndex(['price_group', 'user_id', 'created_at_ymd']);

            $table->dropIndex(['price_group', 'country']);
            $table->dropIndex(['price_group', 'user_id']);
            $table->dropIndex(['country', 'user_id']);

            $table->dropIndex(['price_group', 'created_at_ymd']);
            $table->dropIndex(['country', 'created_at_ymd']);

            $table->dropIndex(['user_id', 'created_at_ymd']);


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

            //delete old
            $table->index(['user_id']);
            $table->index(['created_at_ymd']);
            $table->index(['end_date_ymd']);
            $table->index(['price_group']);
            $table->index(['country']);


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
}
