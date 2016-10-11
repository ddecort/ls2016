<?php namespace wildfire\Athlete\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireAthleteInCategory extends Migration
{
    public function up()
    {
        Schema::table('wildfire_athlete_in_category', function($table)
        {
            $table->renameColumn('product_category_id', 'category_id');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_athlete_in_category', function($table)
        {
            $table->renameColumn('category_id', 'product_category_id');
        });
    }
}
