<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippet5 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->integer('slide_id')->nullable();
            $table->renameColumn('feed_id', 'page_id');
            $table->dropColumn('feed_number');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->dropColumn('slide_id');
            $table->renameColumn('page_id', 'feed_id');
            $table->smallInteger('feed_number')->nullable()->default(1);
        });
    }
}