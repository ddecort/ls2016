<?php namespace wildfire\Athlete\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireAthleteAthlete5 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_athlete_athlete', function($table)
        {
            $table->integer('sort_order')->nullable(false)->default(1000)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_athlete_athlete', function($table)
        {
            $table->integer('sort_order')->nullable()->default(null)->change();
        });
    }
}