<?php namespace wildfire\Athlete\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireAthleteAthlete extends Migration
{
    public function up()
    {
        Schema::table('wildfire_athlete_athlete', function($table)
        {
            $table->text('description')->nullable();
            $table->smallInteger('display_size')->default(1);
            $table->boolean('active')->default(1);
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_athlete_athlete', function($table)
        {
            $table->dropColumn('description');
            $table->dropColumn('display_size');
            $table->dropColumn('active');
        });
    }
}
