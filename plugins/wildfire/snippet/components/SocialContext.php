<?php namespace wildfire\Snippet\Components;

use Request;
use Cms\Classes\ComponentBase;
use wildfire\Snippet\Models\InstagramPost;
use wildfire\Snippet\Models\Page as Pagemodel;

class SocialContext extends ComponentBase
{
    public $section;
    public $igpost;
    public $sportname;

    public function componentDetails()
    {
        return [
            'name'        => 'Contextual Social Links', 
            'description' => 'Output an instagram post based on current sport' 
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        list($section, $sport) = Pagemodel::findSectionAndSport(Request::path(), $this->page, true);

        if ($sport == 'lacrosse')
        {
            $social = [
                'facebook' => 'http://facebook.com/LizardSkinsLacrosse',
                'instagram' => 'http://instagram.com/LizardSkinsLacrosse',
                'twitter' => 'http://twitter.com/LzSkinsLacrosse'
            ];
        }
        else if ($sport == 'baseball' || $section == 'sports') 
        {
            $social = [
                'facebook' => 'https://www.facebook.com/LizardSkinsBaseball',
                'instagram' => 'https://www.instagram.com/lizardskinsbaseball/',
                'twitter' => 'https://twitter.com/LzSkinsBaseball',
                'youtube' => 'https://www.youtube.com/user/LizardSkinsProducts'
            ];
        }
        else 
        {
            //cycling
            $social = [
                'facebook' => 'http://www.facebook.com/pages/Lizard-Skins/137409622991261',
                'instagram' => 'https://www.instagram.com/lizardskinscycling/',
                'twitter' => 'http://twitter.com/lizard_skins',
                'youtube' => 'https://www.youtube.com/user/LizardSkinsProducts'
            ];
        }

        $this->page['social'] = $social;
    }
}
