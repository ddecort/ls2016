<?php namespace wildfire\Athlete\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteWildfireAthleteAthleteSport extends Migration
{
    public function up()
    {
        Schema::dropIfExists('wildfire_athlete_athlete_sport');
    }
    
    public function down()
    {
        Schema::create('wildfire_athlete_athlete_sport', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('athlete_id');
            $table->integer('sport_id');
        });
    }
}
