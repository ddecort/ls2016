<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetPage extends Migration
{
    public function up()
    {
        Schema::rename('wildfire_snippet_feed', 'wildfire_snippet_page');
    }
    
    public function down()
    {
        Schema::rename('wildfire_snippet_page', 'wildfire_snippet_feed');
    }
}
