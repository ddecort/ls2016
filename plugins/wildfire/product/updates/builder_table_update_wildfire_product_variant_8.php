<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductVariant8 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->bigInteger('shopify_product_id')->nullable()->unsigned(false)->default(null)->change();
            $table->bigInteger('shopify_variant_id')->nullable()->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->integer('shopify_product_id')->nullable()->unsigned(false)->default(null)->change();
            $table->integer('shopify_variant_id')->nullable()->unsigned(false)->default(null)->change();
        });
    }
}
