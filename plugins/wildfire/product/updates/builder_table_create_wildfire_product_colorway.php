<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireProductColorway extends Migration
{
    public function up()
    {
        Schema::create('wildfire_product_colorway', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('product_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_product_colorway');
    }
}
