<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetInstagram3 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_instagram', function($table)
        {
            $table->increments('id')->unsigned()->change();
            $table->bigInteger('ig_id')->unsigned()->change();
            $table->renameColumn('ig_shortcode', 'type');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_instagram', function($table)
        {
            $table->increments('id')->unsigned(false)->change();
            $table->bigInteger('ig_id')->unsigned(false)->change();
            $table->renameColumn('type', 'ig_shortcode');
        });
    }
}
