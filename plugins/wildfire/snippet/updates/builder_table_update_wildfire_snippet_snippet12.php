<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippet12 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->boolean('is_video')->default(0);
            $table->smallInteger('split_width')->default(2)->change();
            $table->dropColumn('video_aspect_ratio');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->dropColumn('is_video');
            $table->smallInteger('split_width')->default(2)->change();
            $table->decimal('video_aspect_ratio', 10, 4)->default(1.78);
        });
    }
}