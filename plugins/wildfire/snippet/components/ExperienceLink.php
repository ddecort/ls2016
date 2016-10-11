<?php namespace wildfire\Snippet\Components;

use App;
use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use wildfire\Snippet\Models\Page as Pagemodel;

class ExperienceLink extends ComponentBase
{
    public $section;
    public $sportname;

    public function componentDetails()
    {
        return [
            'name'        => 'Contextual Experience Link', 
            'description' => 'Output a link to the experience page based on current sport' 
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        list($section, $sport) = Pagemodel::findSectionAndSport(Request::path(), $this->page, true);

        if ($sport)
        {
            $prefix = '/'.$sport;
        }
        else
        {
            $prefix = '/'.($section ? $section : 'cycling');
        }

        $this->page['prefix'] = $prefix;
    }
}
