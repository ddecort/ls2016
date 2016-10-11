<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteWildfireSnippetSnippetSlide extends Migration
{
    public function up()
    {
        Schema::dropIfExists('wildfire_snippet_snippet_slide');
    }
    
    public function down()
    {
        Schema::create('wildfire_snippet_snippet_slide', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('snippet_id');
            $table->integer('slide_id');
            $table->integer('sort_order')->default(1);
        });
    }
}
