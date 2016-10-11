<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetFeed extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_feed', function($table)
        {
            $table->string('name')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_feed', function($table)
        {
            $table->integer('name')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
}
