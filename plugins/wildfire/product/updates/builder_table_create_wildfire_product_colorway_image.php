<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireProductColorwayImage extends Migration
{
    public function up()
    {
        Schema::create('wildfire_product_colorway_image', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('colorway_id');
            $table->integer('image_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_product_colorway_image');
    }
}
