<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippetCategory4 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet_category', function($table)
        {
            $table->boolean('for_product')->default(1)->change();
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet_category', function($table)
        {
            $table->boolean('for_product')->default(null)->change();
        });
    }
}
