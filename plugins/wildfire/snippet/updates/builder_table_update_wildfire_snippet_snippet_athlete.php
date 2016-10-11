<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippetAthlete extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet_athlete', function($table)
        {
            $table->integer('sort_order')->default(1);
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet_athlete', function($table)
        {
            $table->dropColumn('sort_order');
        });
    }
}
