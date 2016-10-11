<?php namespace wildfire\Athlete\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireAthleteAthlete3 extends Migration
{
 public function up()
{
    Schema::table('wildfire_athlete_athlete', function($table)
    {
        $table->integer('sort_order')->nullable();
    });
}

public function down()
{
    Schema::table('wildfire_athlete_athlete', function($table)
    {
        $table->dropColumn('sort_order');
    });
}
}