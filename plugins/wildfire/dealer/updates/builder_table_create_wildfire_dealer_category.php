<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireDealerCategory extends Migration
{
    public function up()
    {
        Schema::create('wildfire_dealer_category', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('name');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_dealer_category');
    }
}
