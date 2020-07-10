<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdsFixIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads_meta', function (Blueprint $table) {
            // dropping indexes staring with range
            $table->dropIndex(['price_group', 'country', 'user_id', 'created_at_ymd']);
            $table->dropIndex(['price_group', 'country', 'user_id']); // this index is almost the same as previes
            $table->dropIndex(['price_group', 'country', 'created_at_ymd']);
            $table->dropIndex(['price_group', 'user_id', 'created_at_ymd']);
            $table->dropIndex(['price_group', 'created_at_ymd']);

            //
            $table->dropIndex(['country', 'user_id']); //will be replaced with better one
            $table->index(['country', 'user_id', 'created_at_ymd', 'price_group']);

            $table->dropIndex(['country', 'created_at_ymd']);//will be replaced with better one
            $table->index(['country', 'created_at_ymd', 'price_group']);


            $table->dropIndex(['user_id', 'created_at_ymd']); // will be replaced with better one
            $table->index(['user_id', 'created_at_ymd', 'price_group']); // will be replaced with better one


            $table->index(['price']); // for max/min price

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
             // dropping indexes staring with range
             $table->index(['price_group', 'country', 'user_id', 'created_at_ymd']);
             $table->index(['price_group', 'country', 'user_id']); // this index is almost the same as previes
             $table->index(['price_group', 'country', 'created_at_ymd']);
             $table->index(['price_group', 'user_id', 'created_at_ymd']);
             $table->index(['price_group', 'created_at_ymd']);

             //
             $table->index(['country', 'user_id']); //will be replaced with better one
             $table->dropIndex(['country', 'user_id', 'created_at_ymd', 'price_group']);

             $table->index(['country', 'created_at_ymd']);//will be replaced with better one
             $table->dropIndex(['country', 'created_at_ymd', 'price_group']);


             $table->index(['user_id', 'created_at_ymd']); // will be replaced with better one
             $table->dropIndex(['user_id', 'created_at_ymd', 'price_group']); // will be replaced with better one


             $table->dropIndex(['price']); // for max/min price
        });
    }
}
