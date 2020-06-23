<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoriesTreeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign('categories_pid_foreign');
            $table->dropColumn('pid');
        });

        Schema::create('categories_tree', function (Blueprint $table) {

            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('child_id');
            $table->smallInteger('level');


            $table->foreign('parent_id')
                ->references('id')
                ->on('categories');

            $table->foreign('child_id')
                ->references('id')
                ->on('categories');

            $table->unique(['parent_id', 'child_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories_tree');

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('pid')->nullable();

            $table->foreign('pid')
                ->references('id')
                ->on('categories');
        });
    }
}
