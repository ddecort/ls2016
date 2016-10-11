<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippetCategory3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet_category', function($table)
        {
            $table->boolean('for_category')->default(1);
            $table->boolean('for_product')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet_category', function($table)
        {
            $table->dropColumn('for_category');
            $table->dropColumn('for_product');
        });
    }
}
