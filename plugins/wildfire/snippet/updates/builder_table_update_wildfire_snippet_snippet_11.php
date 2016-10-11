<?php namespace wildfire\Snippet\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateWildfireSnippetSnippet11 extends Migration
{
    public function up()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->smallInteger('split_width')->nullable()->unsigned()->default(2);
            $table->boolean('video_border')->default(0);
            $table->boolean('category_show_swatch')->default(0);
            $table->boolean('category_top_left')->default(0);
            $table->integer('feed_category_id')->nullable();
            $table->smallInteger('feed_number')->nullable()->unsigned();
            $table->boolean('filter_color')->default(0);
            $table->boolean('filter_subcategory')->default(0);
            $table->string('instagram_user')->nullable();
            $table->string('instagram_hashtag')->nullable();
            $table->string('instagram_post_id')->nullable();
            $table->renameColumn('video_url', 'filter_subcategory_label');
        });
    }
    
    public function down()
    {
        Schema::table('wildfire_snippet_snippet', function($table)
        {
            $table->dropColumn('split_width');
            $table->dropColumn('video_border');
            $table->dropColumn('category_show_swatch');
            $table->dropColumn('category_top_left');
            $table->dropColumn('feed_category_id');
            $table->dropColumn('feed_number');
            $table->dropColumn('filter_color');
            $table->dropColumn('filter_subcategory');
            $table->dropColumn('instagram_user');
            $table->dropColumn('instagram_hashtag');
            $table->dropColumn('instagram_post_id');
            $table->renameColumn('filter_subcategory_label', 'video_url');
        });
    }
}
