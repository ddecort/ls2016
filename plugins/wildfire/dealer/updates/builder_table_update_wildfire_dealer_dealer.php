<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireDealerDealer extends Migration
{
    public function up()
    {
        Schema::rename('wildfire_dealer_', 'wildfire_dealer_dealer');
        Schema::table('wildfire_dealer_dealer', function($table)
        {
            $table->increments('id')->unsigned()->change();
        });
    }
    
    public function down()
    {
        Schema::rename('wildfire_dealer_dealer', 'wildfire_dealer_');
        Schema::table('wildfire_dealer_', function($table)
        {
            $table->increments('id')->unsigned(false)->change();
        });
    }
}
