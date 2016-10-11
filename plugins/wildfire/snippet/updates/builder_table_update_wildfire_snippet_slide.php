<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSlide extends Migration
{
    public function up()
    {
        Schema::rename('wildfire_snippet_snippet', 'wildfire_snippet_slide');
    }
    
    public function down()
    {
        Schema::rename('wildfire_snippet_slide', 'wildfire_snippet_snippet');
    }
}
