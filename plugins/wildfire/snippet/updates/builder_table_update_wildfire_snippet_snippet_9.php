<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippet9 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->text('video_iframe')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->dropColumn('video_iframe');
        });
    }
}
