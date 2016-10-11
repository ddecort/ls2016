<?php namespace wildfire\Athlete\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireAthleteAthlete extends Migration
{
    public function up()
    {
        Schema::create('wildfire_athlete_athlete', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('team')->nullable();
            $table->string('nationality')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('youtube')->nullable();
            $table->text('highlights')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_athletes_main');
    }
}