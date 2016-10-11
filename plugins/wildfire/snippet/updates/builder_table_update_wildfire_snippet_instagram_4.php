<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetInstagram4 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_instagram', function($table)
        {
            $table->increments('id')->unsigned()->change();
            $table->string('ig_id')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_instagram', function($table)
        {
            $table->increments('id')->unsigned(false)->change();
            $table->bigInteger('ig_id')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
}
