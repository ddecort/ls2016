<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetInstagram2 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_instagram', function($table)
        {
            $table->string('link');
            $table->increments('id')->unsigned()->change();
            $table->bigInteger('ig_id')->unsigned()->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_instagram', function($table)
        {
            $table->dropColumn('link');
            $table->increments('id')->unsigned(false)->change();
            $table->bigInteger('ig_id')->unsigned(false)->change();
        });
    }
}
