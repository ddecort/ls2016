<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireDealerInCategory extends Migration
{
    public function up()
    {
        Schema::create('wildfire_dealer_in_category', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('dealer_id');
            $table->integer('category_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_dealer_in_category');
    }
}
