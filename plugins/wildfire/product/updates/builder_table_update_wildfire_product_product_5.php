<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductProduct5 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_product', function($table)
        {
            $table->string('slug')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_product', function($table)
        {
            $table->dropColumn('slug');
        });
    }
}
