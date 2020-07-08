<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdsTableSplitCreatedToAtDateAdnTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->date('created_at_date')->index()->nullable();
            $table->time('created_at_time')->nullable();
        });

        \DB::table('ads')->update([
            'created_at_date' => \DB::raw("DATE(created_at)"),
            'created_at_time' => \DB::raw("Time(created_at)")
        ]);

        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn(['created_at']);
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
            $table->timestamp('created_at')->nullable();
        });

        \DB::table('ads')->update([
            'created_at' => \DB::raw("cast(concat(created_at_date, ' ', created_at_time) as datetime)"),
        ]);

        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn(['created_at_date', 'created_at_time']);
        });
    }
}
