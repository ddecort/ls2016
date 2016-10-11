<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductSize3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_size', function($table)
        {
            $table->integer('variant_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_size', function($table)
        {
            $table->dropColumn('variant_id');
        });
    }
}
