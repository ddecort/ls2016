<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductVariant12 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->integer('colorway_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->dropColumn('colorway_id');
        });
    }
}
