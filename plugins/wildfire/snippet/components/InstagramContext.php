<?php namespace wildfire\Snippet\Components;

use Request;
use Cms\Classes\ComponentBase;
use wildfire\Snippet\Models\InstagramPost;
use wildfire\Snippet\Models\Page as Pagemodel;

class InstagramContext extends ComponentBase
{
    public $section;
    public $igpost;
    public $sportname;

    public function componentDetails()
    {
        return [
            'name'        => 'Contextual Instagram', 
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

        $acc = null;
        if ($sport == 'lacrosse')
        {
            $acc = 'lizardskinslacrosse';
        }
        else if ($sport == 'baseball' || $section == 'sports')
        {
            $acc = 'lizardskinsbaseball';
        }
        else if ($section == 'cycling')
        {
            $acc = 'lizardskinscycling';
        }

        if ($acc)
        {
            $this->igpost = $this->page['igpost'] = InstagramPost::pickUnusedPost(InstagramPost::getPosts($acc));
        }
    }
}
