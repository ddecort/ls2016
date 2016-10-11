<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductSize4 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_size', function($table)
        {
            $table->smallInteger('sort_order')->nullable(false)->default(1000)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_size', function($table)
        {
            $table->smallInteger('sort_order')->nullable()->default(null)->change();
        });
    }
}
