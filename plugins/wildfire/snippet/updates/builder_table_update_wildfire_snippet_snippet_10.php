<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippet10 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->boolean('related_othercats')->default(1);
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->dropColumn('related_othercats');
        });
    }
}
