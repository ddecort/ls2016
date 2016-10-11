<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireSnippetSnippet extends Migration
{
    public function up()
    {
        Schema::create('wildfire_snippet_snippet', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('type')->default('link');
            $table->string('heading_text')->nullable();
            $table->string('heading_position')->default('left');
            $table->string('heading_position_mobile')->default('top');
            $table->string('subheading')->nullable();
            $table->decimal('aspect_ratio', 10, 0)->default(1.78);
            $table->string('link_url')->nullable();
            $table->string('button_text')->nullable();
            $table->string('video_url')->nullable();
            $table->text('content')->nullable();
            $table->boolean('heading_light')->default(0);
            $table->boolean('heading_light_mobile')->default(0);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_snippet_snippet');
    }
}
