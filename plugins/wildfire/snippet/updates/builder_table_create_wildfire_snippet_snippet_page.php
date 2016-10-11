<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireSnippetSnippetPage extends Migration
{
    public function up()
    {
        Schema::create('wildfire_snippet_snippet_page', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('page_id');
            $table->integer('snippet_id');
            $table->integer('sequence')->default(1);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_snippet_snippet_page');
    }
}
