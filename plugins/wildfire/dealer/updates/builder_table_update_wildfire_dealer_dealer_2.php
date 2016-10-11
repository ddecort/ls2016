<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireDealerDealer2 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_dealer_dealer', function($table)
        {
            $table->increments('id')->unsigned()->change();
            $table->renameColumn('town', 'city');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_dealer_dealer', function($table)
        {
            $table->increments('id')->unsigned(false)->change();
            $table->renameColumn('city', 'town');
        });
    }
}
