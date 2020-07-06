<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdsAddComplexIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->index(['country', 'user_id', 'created_at_date']);
            $table->index(['country', 'user_id']);
            $table->index(['country', 'created_at_date']);
            $table->index(['user_id', 'created_at_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropIndex(['country', 'user_id', 'created_at_date']);
            $table->dropIndex(['country', 'user_id']);
            $table->dropIndex(['country', 'created_at_date']);
            $table->dropIndex(['user_id', 'created_at_date']);
        });
    }
}
