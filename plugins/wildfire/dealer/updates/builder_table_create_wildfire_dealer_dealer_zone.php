<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireDealerDealerZone extends Migration
{
    public function up()
    {
        Schema::create('wildfire_dealer_dealer_zone', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('dealer_id');
            $table->integer('zone_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_dealer_dealer_zone');
    }
}
