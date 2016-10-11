<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippet6 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->string('heading_text')->nullable();
            $table->string('heading_position')->default('left');
            $table->string('heading_position_mobile')->default('top');
            $table->string('subheading')->nullable();
            $table->decimal('aspect_ratio', 10, 4)->default(1.78);
            $table->string('link_url')->nullable();
            $table->string('button_text')->nullable();
            $table->string('video_url')->nullable();
            $table->text('content')->nullable();
            $table->boolean('heading_light')->default(0);
            $table->boolean('heading_light_mobile')->default(0);
            $table->boolean('hidden')->default(0);
            $table->decimal('video_aspect_ratio', 10, 4)->default(1.78);
            $table->integer('parent_snippet_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->dropColumn('heading_text');
            $table->dropColumn('heading_position');
            $table->dropColumn('heading_position_mobile');
            $table->dropColumn('subheading');
            $table->dropColumn('aspect_ratio');
            $table->dropColumn('link_url');
            $table->dropColumn('button_text');
            $table->dropColumn('video_url');
            $table->dropColumn('content');
            $table->dropColumn('heading_light');
            $table->dropColumn('heading_light_mobile');
            $table->dropColumn('hidden');
            $table->dropColumn('video_aspect_ratio');
            $table->dropColumn('parent_snippet_id');
        });
    }
}
