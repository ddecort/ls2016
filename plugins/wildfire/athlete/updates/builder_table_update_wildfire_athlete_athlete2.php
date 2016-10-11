<?php namespace wildfire\Athlete\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireAthleteAthlete2 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_athlete_athlete', function($table)
        {
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->smallInteger('display_size')->default(1)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_athlete_athlete', function($table)
        {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->smallInteger('display_size')->default(1)->change();
        });
    }
}