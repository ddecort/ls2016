<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductCategory extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_category', function($table)
        {
            $table->string('slug');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_category', function($table)
        {
            $table->dropColumn('slug');
        });
    }
}
