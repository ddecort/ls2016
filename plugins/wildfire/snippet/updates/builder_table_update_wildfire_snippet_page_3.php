<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetPage3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_page', function($table)
        {
            $table->string('slug')->nullable(false)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_page', function($table)
        {
            $table->string('slug', 255)->nullable()->change();
        });
    }
}
