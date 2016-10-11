<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductVariant3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->smallInteger('rotataion_numimages');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_variant', function($table)
        {
            $table->dropColumn('rotataion_numimages');
        });
    }
}
