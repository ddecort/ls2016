<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireDealerCategories2 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_dealer_categories', function($table)
        {
            $table->string('name', 255)->nullable(false)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_dealer_categories', function($table)
        {
            $table->string('name', 255)->nullable()->change();
        });
    }
}
