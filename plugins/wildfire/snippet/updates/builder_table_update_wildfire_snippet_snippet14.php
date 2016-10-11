<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippet14 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->string('category_info_position')->default('bottom');
            $table->smallInteger('split_width')->default(null)->change();
            $table->dropColumn('category_top_left');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->dropColumn('category_info_position');
            $table->smallInteger('split_width')->default(2)->change();
            $table->boolean('category_top_left');
        });
    }
}