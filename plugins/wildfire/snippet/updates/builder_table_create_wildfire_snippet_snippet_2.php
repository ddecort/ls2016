<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireSnippetSnippet2 extends Migration
{
    public function up()
    {
        Schema::create('wildfire_snippet_snippet', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('type')->default('slide');
            $table->smallInteger('stream_number')->nullable()->default(1);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_snippet_snippet');
    }
}
