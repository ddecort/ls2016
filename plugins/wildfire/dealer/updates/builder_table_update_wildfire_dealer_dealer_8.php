<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireDealerDealer8 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_dealer_dealer', function($table)
        {
            $table->string('postal', 255)->nullable()->change();
            $table->string('street2', 255)->nullable()->change();
            $table->string('street1', 255)->nullable()->change();
            $table->string('phone', 255)->nullable()->change();
            $table->string('website', 255)->nullable()->change();
            $table->string('email', 255)->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_dealer_dealer', function($table)
        {
            $table->string('postal', 255)->nullable(false)->change();
            $table->string('street2', 255)->nullable(false)->change();
            $table->string('street1', 255)->nullable(false)->change();
            $table->string('phone', 255)->nullable(false)->change();
            $table->string('website', 255)->nullable(false)->change();
            $table->string('email', 255)->nullable(false)->change();
        });
    }
}
