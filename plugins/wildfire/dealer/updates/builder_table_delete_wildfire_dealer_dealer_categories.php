<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteWildfireDealerDealerCategories extends Migration
{
    public function up()
    {
        Schema::dropIfExists('wildfire_dealer_dealer_categories');
    }
    
    public function down()
    {
        Schema::create('wildfire_dealer_dealer_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('dealer_id');
            $table->integer('category_id');
            $table->primary(['dealer_id','category_id']);
        });
    }
}
