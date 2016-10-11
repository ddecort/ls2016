<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireProductSize extends Migration
{
    public function up()
    {
        Schema::create('wildfire_product_size', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_product_size');
    }
}
