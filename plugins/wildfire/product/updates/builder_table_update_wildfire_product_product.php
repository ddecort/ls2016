<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductProduct extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_product', function($table)
        {
            $table->bigInteger('shopify_id')->nullable()->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_product', function($table)
        {
            $table->integer('shopify_id')->nullable()->unsigned(false)->default(null)->change();
        });
    }
}
