<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireSnippetSnippetAthlete extends Migration
{
    public function up()
    {
        Schema::create('wildfire_snippet_snippet_athlete', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('snippet_id');
            $table->integer('athlete_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_snippet_snippet_athlete');
    }
}
