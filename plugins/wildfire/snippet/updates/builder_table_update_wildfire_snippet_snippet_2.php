<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippet2 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->boolean('private')->default(0);
            $table->decimal('video_aspect_ratio', 10, 0)->default(1.78);
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->dropColumn('private');
            $table->dropColumn('video_aspect_ratio');
        });
    }
}
