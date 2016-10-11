<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductImage2 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_image', function($table)
        {
            $table->integer('sort_order')->default(1000)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_image', function($table)
        {
            $table->integer('sort_order')->default(1)->change();
        });
    }
}
