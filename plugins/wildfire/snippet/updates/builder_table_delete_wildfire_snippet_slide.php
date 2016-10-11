<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteWildfireSnippetSlide extends Migration
{
    public function up()
    {
        Schema::dropIfExists('wildfire_snippet_slide');
    }
    
    public function down()
    {
        Schema::create('wildfire_snippet_slide', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 255)->nullable();
            $table->string('type', 255)->default('link');
            $table->string('heading_text', 255)->nullable();
            $table->string('heading_position', 255)->default('left');
            $table->string('heading_position_mobile', 255)->default('top');
            $table->string('subheading', 255)->nullable();
            $table->decimal('aspect_ratio', 10, 4)->default(1.78);
            $table->string('link_url', 255)->nullable();
            $table->string('button_text', 255)->nullable();
            $table->string('video_url', 255)->nullable();
            $table->text('content')->nullable();
            $table->boolean('heading_light');
            $table->boolean('heading_light_mobile');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
            $table->boolean('private');
            $table->decimal('video_aspect_ratio', 10, 4)->default(1.78);
            $table->integer('snippet_id')->nullable();
        });
    }
}
