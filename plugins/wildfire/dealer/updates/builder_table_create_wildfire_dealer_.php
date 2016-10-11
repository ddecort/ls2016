<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireDealer extends Migration
{
    public function up()
    {
        Schema::create('wildfire_dealer_', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('zone');
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('town')->nullable();
            $table->string('postal');
            $table->string('street2');
            $table->string('street1');
            $table->boolean('baseball')->default(0);
            $table->boolean('hockey')->default(0);
            $table->boolean('lacrosse')->default(0);
            $table->boolean('cycling')->default(0);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_dealer_');
    }
}
