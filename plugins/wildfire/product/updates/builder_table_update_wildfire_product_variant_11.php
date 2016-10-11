<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductVariant11 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->dropColumn('color_id');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->integer('color_id')->nullable();
        });
    }
}
