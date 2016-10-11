<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductInCategory extends Migration
{
    public function up()
    {
        Schema::rename('wildfire_product_product_category', 'wildfire_product_in_category');
    }
    
    public function down()
    {
        Schema::rename('wildfire_product_in_category', 'wildfire_product_product_category');
    }
}
