<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductImage3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_image', function($table)
        {
            $table->string('label')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_image', function($table)
        {
            $table->dropColumn('label');
        });
    }
}
