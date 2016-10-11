<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireSnippetSnippetCategory extends Migration
{
    public function up()
    {
        Schema::create('wildfire_snippet_snippet_category', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('snippet_id');
            $table->integer('category_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_snippet_snippet_category');
    }
}
