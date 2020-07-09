<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdsMetaTableAddIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads_meta', function (Blueprint $table) {

            $table->index(['price_group', 'price', 'country', 'user_id', 'created_at_ymd']);
            $table->index(['country', 'user_id', 'created_at_ymd']);
            $table->index(['user_id', 'created_at_ymd']);
            $table->index(['created_at_ymd']);


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

            $table->dropIndex(['price_group', 'price', 'country', 'user_id', 'created_at_ymd']);
            $table->dropIndex(['country', 'user_id', 'created_at_ymd']);
            $table->dropIndex(['user_id', 'created_at_ymd']);
            $table->dropIndex(['created_at_ymd']);

        });
    }
}
