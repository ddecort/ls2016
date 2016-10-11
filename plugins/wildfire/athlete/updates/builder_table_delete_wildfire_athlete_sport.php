<?php namespace wildfire\Athlete\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteWildfireAthleteSport extends Migration
{
    public function up()
    {
        Schema::dropIfExists('wildfire_athlete_sport');
    }
    
    public function down()
    {
        Schema::create('wildfire_athlete_sport', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 255);
            $table->integer('parent_id')->nullable();
            $table->integer('nest_left')->nullable();
            $table->integer('nest_right')->nullable();
            $table->integer('nest_depth')->nullable();
            $table->boolean('show_selection')->default(1);
        });
    }
}
