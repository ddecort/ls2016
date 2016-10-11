<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippetPage3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet_page', function($table)
        {
            $table->integer('sort_order')->default(0)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet_page', function($table)
        {
            $table->integer('sort_order')->default(1000)->change();
        });
    }
}
