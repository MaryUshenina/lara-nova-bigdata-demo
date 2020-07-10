<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAdsMEtaFixIndexesPart2 extends Migration
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
            $table->dropindex(['price_group', 'country']);
            $table->dropindex(['price_group', 'user_id']);

            $table->dropindex(['user_id']); // is used as start of ads_meta_user_id_created_at_ymd_price_group_index
            $table->dropindex(['country']); // is used as start of ads_meta_country_user_id_created_at_ymd_price_group_index

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

            $table->index(['price_group', 'country']);
            $table->index(['price_group', 'user_id']);

            $table->index(['user_id']);
            $table->index(['country']);

        });
    }
}
