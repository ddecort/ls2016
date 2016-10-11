<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductVariant extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->string('barcode')->nullable();
            $table->boolean('taxable')->default(1);
            $table->decimal('weight', 10, 0)->nullable();
            $table->increments('id')->unsigned()->change();
            $table->integer('shopify_product_id')->nullable()->change();
            $table->integer('shopify_variant_id')->nullable()->change();
            $table->smallInteger('shopify_inventory')->nullable()->change();
            $table->string('sku')->nullable()->change();
            $table->renameColumn('shopify_price', 'price');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->dropColumn('barcode');
            $table->dropColumn('taxable');
            $table->dropColumn('weight');
            $table->increments('id')->unsigned(false)->change();
            $table->integer('shopify_product_id')->nullable(false)->change();
            $table->integer('shopify_variant_id')->nullable(false)->change();
            $table->smallInteger('shopify_inventory')->nullable(false)->change();
            $table->string('sku', 255)->nullable(false)->change();
            $table->renameColumn('price', 'shopify_price');
        });
    }
}
