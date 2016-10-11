<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetPage2 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_page', function($table)
        {
            $table->string('slug')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_page', function($table)
        {
            $table->dropColumn('slug');
        });
    }
}
