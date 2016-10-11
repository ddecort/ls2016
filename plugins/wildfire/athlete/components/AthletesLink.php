<?php namespace wildfire\Athlete\Components;

use App;
use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use wildfire\Snippet\Models\Page as Pagemodel;

class AthletesLink extends ComponentBase
{
    public $section;
    public $sportname;

    public function componentDetails()
    {
        return [
            'name'        => 'Contextual Athletes Link', 
            'description' => 'Output a link to the athletes listing page based on current sport' 
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
