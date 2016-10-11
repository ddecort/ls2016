<?php namespace wildfire\Athlete\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireAthleteInCategory extends Migration
{
    public function up()
    {
        Schema::create('wildfire_athlete_in_category', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('athlete_id');
            $table->integer('product_category_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_athlete_in_category');
    }
}
