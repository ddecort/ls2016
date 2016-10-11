<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductCategory3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_category', function($table)
        {
            $table->string('caption_position')->default('left');
            $table->boolean('caption_light')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_category', function($table)
        {
            $table->dropColumn('caption_position');
            $table->dropColumn('caption_light');
        });
    }
}
