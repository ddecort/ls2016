<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippet14 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->boolean('mobile_show_header')->default(1);
            $table->boolean('mobile_show_subheader')->default(1);
            $table->boolean('mobile_show_button')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->dropColumn('mobile_show_header');
            $table->dropColumn('mobile_show_subheader');
            $table->dropColumn('mobile_show_button');
        });
    }
}
