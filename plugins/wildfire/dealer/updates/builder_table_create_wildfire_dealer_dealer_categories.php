<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireDealerDealerCategories extends Migration
{
    public function up()
    {
        Schema::create('wildfire_dealer_dealer_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('dealer_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->primary(['dealer_id', 'category_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_dealer_dealer_categories');
    }
}