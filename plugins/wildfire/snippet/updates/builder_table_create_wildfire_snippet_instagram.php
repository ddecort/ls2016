<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateWildfireSnippetInstagram extends Migration
{
    public function up()
    {
        Schema::create('wildfire_snippet_instagram', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('id')->unsigned();
            $table->bigInteger('ig_id')->unsigned();
            $table->string('ig_shortcode');
            $table->dateTime('date_posted');
            $table->string('account');
            $table->string('tags')->nullable();
            $table->text('data');
            $table->boolean('blacklisted')->nullable()->default(0);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('wildfire_snippet_instagram');
    }
}
