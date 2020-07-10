<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdsMetaAddIndexesPart3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads_meta', function (Blueprint $table) {
            $table->index(['country', 'price_group']);
            $table->index(['user_id', 'price_group']);
            $table->index(['created_at_ymd', 'price_group']);
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
            $table->dropIndex(['country', 'price_group']);
            $table->dropIndex(['user_id', 'price_group']);
            $table->dropIndex(['created_at_ymd', 'price_group']);
        });
    }
}
