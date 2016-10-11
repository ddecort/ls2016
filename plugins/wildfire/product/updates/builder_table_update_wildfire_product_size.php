<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductSize extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_size', function($table)
        {
            $table->string('shortname')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_size', function($table)
        {
            $table->dropColumn('shortname');
        });
    }
}
