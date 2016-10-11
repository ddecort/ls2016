<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductProductCategory extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_product_category', function($table)
        {
            $table->integer('sort_order')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_product_category', function($table)
        {
            $table->dropColumn('sort_order');
        });
    }
}
