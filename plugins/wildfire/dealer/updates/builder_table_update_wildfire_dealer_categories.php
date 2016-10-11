<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireDealerCategories extends Migration
{
    public function up()
    {
        Schema::rename('wildfire_dealer_category', 'wildfire_dealer_categories');
    }
    
    public function down()
    {
        Schema::rename('wildfire_dealer_categories', 'wildfire_dealer_category');
    }
}
