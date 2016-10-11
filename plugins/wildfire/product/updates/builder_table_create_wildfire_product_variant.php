<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireProductVariant extends Migration
{
    public function up()
    {
        Schema::create('wildfire_product_variant', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('product_id');
            $table->integer('shopify_product_id');
            $table->integer('shopify_variant_id');
            $table->smallInteger('shopify_inventory');
            $table->decimal('shopify_price', 10, 0);
            $table->string('sku');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_product_variant');
    }
}
