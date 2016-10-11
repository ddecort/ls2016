<?php namespace wildfire\Dealer\Components;

use Request;
use Cms\Classes\ComponentBase;
use wildfire\Snippet\Models\Page as Pagemodel;

class DealersLink extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'Contextual Dealer Locator Link', 
            'description' => 'Output a link to the dealer locator based on current sport' 
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

        $this->page['dprefix'] = $prefix;
    }
}
