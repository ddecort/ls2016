<?php namespace wildfire\Product\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireProductCategory4 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_product_category', function($table)
        {
            $table->boolean('use_for_dealers')->default(1);
            $table->boolean('use_for_athletes')->default(1);
            $table->boolean('use_for_athletes_filter')->default(1);
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_product_category', function($table)
        {
            $table->dropColumn('use_for_dealers');
            $table->dropColumn('use_for_athletes');
            $table->dropColumn('use_for_athletes_filter');
        });
    }
}
