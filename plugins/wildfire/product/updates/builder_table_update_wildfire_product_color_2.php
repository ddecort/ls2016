<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductColor2 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_color', function($table)
        {
            $table->string('hex_code', 6)->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_color', function($table)
        {
            $table->dropColumn('hex_code');
        });
    }
}
