<?php namespace wildfire\Athlete\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireAthleteSport3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_athlete_athlete', function($table)
        {
            $table->boolean('is_team')->default(0);
            $table->string('caption_align')->default('center');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_athlete_athlete', function($table)
        {
            $table->dropColumn('is_team');
            $table->dropColumn('caption_align');
        });
    }
}