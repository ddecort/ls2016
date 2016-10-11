<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireDealerDealer13 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_dealer_dealer', function($table)
        {
            $table->decimal('latitude', 10, 3)->nullable();
            $table->decimal('longitude', 10, 3)->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_dealer_dealer', function($table)
        {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
}
