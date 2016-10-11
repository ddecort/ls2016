<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteWildfireDealerCategory extends Migration
{
    public function up()
    {
        Schema::dropIfExists('wildfire_dealer_category');
    }
    
    public function down()
    {
        Schema::create('wildfire_dealer_category', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 255);
            $table->integer('parent_id')->nullable();
            $table->integer('nest_left')->nullable();
            $table->integer('nest_right')->nullable();
            $table->integer('nest_depth')->nullable();
        });
    }
}
