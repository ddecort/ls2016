<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireProductColor extends Migration
{
    public function up()
    {
        Schema::create('wildfire_product_color', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_product_color');
    }
}
