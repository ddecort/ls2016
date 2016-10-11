<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireDealerDealer3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_dealer_dealer', function($table)
        {
            $table->boolean('distributor')->default(0);
            $table->increments('id')->unsigned()->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_dealer_dealer', function($table)
        {
            $table->dropColumn('distributor');
            $table->increments('id')->unsigned(false)->change();
        });
    }
}
