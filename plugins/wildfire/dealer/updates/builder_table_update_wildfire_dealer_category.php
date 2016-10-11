<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireDealerCategory extends Migration
{
    public function up()
    {
        Schema::table('wildfire_dealer_category', function($table)
        {
            $table->increments('id');
            $table->string('slug')->nullable();
            $table->string('code')->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('nest_left')->nullable();
            $table->integer('nest_right')->nullable();
            $table->integer('nest_depth')->nullable();
            $table->string('name')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_dealer_category', function($table)
        {
            $table->dropColumn('id');
            $table->dropColumn('slug');
            $table->dropColumn('code');
            $table->dropColumn('parent_id');
            $table->dropColumn('nest_left');
            $table->dropColumn('nest_right');
            $table->dropColumn('nest_depth');
            $table->string('name', 255)->nullable(false)->change();
        });
    }
}