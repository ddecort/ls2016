<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippet3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->smallInteger('feed_number')->nullable()->default(1);
            $table->integer('slideshow_delay')->nullable()->default(3000);
            $table->integer('slideshow_transition')->nullable()->default(500);
            $table->integer('feed_id')->nullable();
            $table->dropColumn('stream_number');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->dropColumn('feed_number');
            $table->dropColumn('slideshow_delay');
            $table->dropColumn('slideshow_transition');
            $table->dropColumn('feed_id');
            $table->smallInteger('stream_number')->nullable()->default(1);
        });
    }
}