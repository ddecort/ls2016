<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductVariant10 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->decimal('price', 10, 2)->change();
            $table->decimal('weight', 10, 3)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->decimal('price', 10, 0)->change();
            $table->decimal('weight', 10, 0)->change();
        });
    }
}
