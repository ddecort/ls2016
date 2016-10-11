<?php namespace wildfire\Snippet;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
               'wildfire\Snippet\Components\Categorycontent' => 'categorycontent',
               'wildfire\Snippet\Components\Pagecontent' => 'pagecontent',
               'wildfire\Snippet\Components\Companycontent' => 'companycontent',
               'wildfire\Snippet\Components\InstagramContext' => 'instagramContext',
               'wildfire\Snippet\Components\SocialContext' => 'socialContext',
               'wildfire\Snippet\Components\SubscribeContext' => 'subscribeContext',
               'wildfire\Snippet\Components\Athletecontent' => 'athletecontent',
               'wildfire\Snippet\Components\ExperienceLink' => 'experiencelink',
               'wildfire\Snippet\Components\Experiencecontent' => 'experiencecontent',
        ];
    }
    

    public function registerSettings()
    {
    }
}
