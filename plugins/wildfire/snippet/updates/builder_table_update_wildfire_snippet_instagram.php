<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetInstagram extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_instagram', function($table)
        {
            $table->increments('id')->unsigned()->change();
            $table->bigInteger('ig_id')->unsigned()->change();
            $table->renameColumn('account', 'user');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_instagram', function($table)
        {
            $table->integer('id')->unsigned(false)->change();
            $table->bigInteger('ig_id')->unsigned(false)->change();
            $table->renameColumn('user', 'account');
        });
    }
}
