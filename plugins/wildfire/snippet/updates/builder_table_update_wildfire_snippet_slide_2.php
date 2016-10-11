<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSlide2 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_slide', function($table)
        {
            $table->string('name', 255)->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_slide', function($table)
        {
            $table->string('name', 255)->nullable(false)->change();
        });
    }
}
