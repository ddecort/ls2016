<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippet4 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}