<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippetPage extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet_page', function($table)
        {
            $table->renameColumn('sequence', 'sort_order');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet_page', function($table)
        {
            $table->renameColumn('sort_order', 'sequence');
        });
    }
}
