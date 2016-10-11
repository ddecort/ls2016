<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetInstagram5 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_instagram', function($table)
        {
            $table->renameColumn('user', 'acct');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_instagram', function($table)
        {
            $table->renameColumn('acct', 'user');
        });
    }
}
