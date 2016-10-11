<?php namespace wildfire\Athlete\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireAthleteAthleteSport extends Migration
{
    public function up()
    {
        Schema::create('wildfire_athlete_athlete_sport', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('athlete_id')->unsigned();
            $table->integer('sport_id')->unsigned();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_athlete_athlete_sport');
    }
}
