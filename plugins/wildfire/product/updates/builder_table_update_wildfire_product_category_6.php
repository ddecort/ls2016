<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductCategory6 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_category', function($table)
        {
            $table->dropColumn('description');
            $table->dropColumn('caption_position');
            $table->dropColumn('caption_light');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_category', function($table)
        {
            $table->string('description', 255)->nullable();
            $table->string('caption_position', 255)->default('left');
            $table->boolean('caption_light');
        });
    }
}
