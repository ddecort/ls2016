<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireDealerDealer11 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_dealer_dealer', function($table)
        {
            $table->string('street')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_dealer_dealer', function($table)
        {
            $table->dropColumn('street');
        });
    }
}
