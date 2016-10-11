<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSlide3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_slide', function($table)
        {
            $table->decimal('aspect_ratio', 10, 4)->change();
            $table->decimal('video_aspect_ratio', 10, 4)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_slide', function($table)
        {
            $table->decimal('aspect_ratio', 10, 0)->change();
            $table->decimal('video_aspect_ratio', 10, 0)->change();
        });
    }
}
