<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdsUpdateTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS ads_after_update;");

        DB::unprepared("CREATE TRIGGER ads_after_update
                AFTER UPDATE
                   ON ads FOR EACH ROW
                BEGIN
                    DELETE FROM ads_meta WHERE ad_id = OLD.id;

                    IF NEW.deleted_at IS null THEN
                        INSERT INTO ads_meta
                        ( ad_id, user_id, country, created_at_ymd, end_date_ymd, price, price_group)
                        VALUES
                        (   NEW.id, NEW.user_id, NEW.country,
                            CONCAT(
                               right(YEAR(NEW.created_at_date), 2),
                               LPAD(MONTH(NEW.created_at_date), 2, 0),
                               LPAD(Day(NEW.created_at_date), 2, 0)
                            ),
                             CONCAT(
                                right(YEAR(NEW.end_date), 2),
                                LPAD(MONTH(NEW.end_date), 2, 0),
                                LPAD(Day(NEW.end_date), 2, 0)
                            ),
                            NEW.price, CEIL (NEW.price / 10000)
                        );
                    END IF;

                END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS ads_after_update;");
    }
}
