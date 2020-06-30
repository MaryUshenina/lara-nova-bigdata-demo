<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeTableViewCategoriesForPidAndMinPid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('DROP VIEW IF EXISTS `categories_tree_view`;');

        DB::statement("CREATE VIEW `categories_tree_view` AS
                SELECT
                   `categories`.id,
                   `categories`.name,

                   (SELECT if(`categories`.id <> t1.parent_id,t1.parent_id, 0 ) FROM categories_tree t1 WHERE t1.child_id= `categories`.id
                    ORDER BY t1.level DESC LIMIT 1) AS min_pid,

                    IFNULL((SELECT t2.parent_id  FROM categories_tree t2 WHERE t2.child_id= `categories`.id
                    ORDER BY t2.level asc LIMIT 1 OFFSET 1), 0) AS 'pid',

                 GROUP_CONCAT(LPAD(ct.parent_id, 4, 0) ORDER BY ct.level DESC) AS 'tree_order',
                 MAX(ct.level) AS 'max_level'
                 FROM
                   `categories`
                   INNER JOIN `categories_tree` AS `ct`
                     ON `ct`.`child_id` = `categories`.`id`
                   WHERE categories.deleted_at IS NULL
                 GROUP BY `categories`.`id`,
                   `categories`.`name`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS `categories_tree_view`;');

        DB::statement("CREATE VIEW `categories_tree_view` AS
                SELECT
                  `categories`.id,
                  `categories`.name,
                  IF(`categories`.id <> MIN(ct.parent_id), MIN(ct.parent_id), 0) AS min_pid,
                  MAX(IF(ct.parent_id < `categories`.id, ct.parent_id, 0)) AS 'pid',

                GROUP_CONCAT(LPAD(ct.parent_id, 4, 0) ORDER BY ct.level DESC) AS 'tree_order',
                MAX(ct.level) AS 'max_level'
                FROM
                  `categories`
                  INNER JOIN `categories_tree` AS `ct`
                    ON `ct`.`child_id` = `categories`.`id`
                  WHERE categories.deleted_at IS NULL
                GROUP BY `categories`.`id`,
                  `categories`.`name`");
    }
}
