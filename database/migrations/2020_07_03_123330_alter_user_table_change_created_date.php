<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserTableChangeCreatedDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('created_at_date')->index()->nullable();
            $table->time('created_at_time')->nullable();

        });

        \DB::statement('UPDATE users SET created_at_date = DATE(created_at), created_at_time = Time(created_at) WHERE created_at_date IS NULL;');

        Schema::table('users', function (Blueprint $table) {
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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable();
        });

        \DB::statement("UPDATE users SET created_at =  cast(concat(created_at_date, ' ', created_at_time) as datetime)");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['created_at_date', 'created_at_time']);
        });
    }
}
