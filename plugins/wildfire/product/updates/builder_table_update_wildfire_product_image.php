<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductImage extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_image', function($table)
        {
            $table->integer('colorway_id')->nullable();
            $table->integer('size_id')->nullable();
            $table->increments('id')->unsigned()->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_image', function($table)
        {
            $table->dropColumn('colorway_id');
            $table->dropColumn('size_id');
            $table->increments('id')->unsigned(false)->change();
        });
    }
}
