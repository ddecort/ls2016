<?php namespace wildfire\Athlete\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireAthleteSport2 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_athlete_sport', function($table)
        {
            $table->boolean('show_selection')->default(1);
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_athlete_sport', function($table)
        {
            $table->dropColumn('show_selection');
        });
    }
}
