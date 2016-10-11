<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireProductImage extends Migration
{
    public function up()
    {
        Schema::create('wildfire_product_image', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('product_id');
            $table->integer('sort_order')->default(1);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_product_image');
    }
}
