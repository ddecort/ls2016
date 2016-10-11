<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireProductVariantHasColor extends Migration
{
    public function up()
    {
        Schema::create('wildfire_product_variant_has_color', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('variant_id');
            $table->integer('color_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_product_variant_has_color');
    }
}
