<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireDealerCategories3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_dealer_categories', function($table)
        {
            $table->dropColumn('slug');
            $table->dropColumn('code');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_dealer_categories', function($table)
        {
            $table->string('slug', 255)->nullable();
            $table->string('code', 255)->nullable();
        });
    }
}
