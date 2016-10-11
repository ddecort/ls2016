<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductProduct3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_product', function($table)
        {
            $table->boolean('active')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_product', function($table)
        {
            $table->dropColumn('active');
        });
    }
}
