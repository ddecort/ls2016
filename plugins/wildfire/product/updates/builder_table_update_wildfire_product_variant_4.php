<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductVariant4 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->boolean('has_color')->default(1);
            $table->boolean('has_size')->default(1);
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->dropColumn('has_color');
            $table->dropColumn('has_size');
        });
    }
}
