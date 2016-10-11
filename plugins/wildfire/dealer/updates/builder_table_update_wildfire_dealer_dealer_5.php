<?php namespace wildfire\Dealer\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireDealerDealer5 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_dealer_dealer', function($table)
        {
            $table->string('phone');
            $table->string('website');
            $table->string('email');
            $table->dropColumn('baseball');
            $table->dropColumn('hockey');
            $table->dropColumn('lacrosse');
            $table->dropColumn('cycling');
            $table->dropColumn('sequence');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_dealer_dealer', function($table)
        {
            $table->dropColumn('phone');
            $table->dropColumn('website');
            $table->dropColumn('email');
            $table->boolean('baseball');
            $table->boolean('hockey');
            $table->boolean('lacrosse');
            $table->boolean('cycling');
            $table->smallInteger('sequence');
        });
    }
}
