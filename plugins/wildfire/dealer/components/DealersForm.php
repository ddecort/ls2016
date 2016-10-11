<?php namespace wildfire\Dealer\Components;

use App;
use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use wildfire\Snippet\Models\Page as Pagemodel;

class DealersForm extends ComponentBase
{
    public $site;
    public $sportname;

    public function componentDetails()
    {
        return [
            'name'        => 'Contextual Dealer Locator Box', 
            'description' => 'Output a search box for the dealer locator based on current sport' 
        ];
    }

    public function defineProperties()
    {
        return [
             'site' => [
                'title' => 'Site',
                'description' => 'Sports or Cycling',
                'type' => 'dropdown',
                'options' => ['Cycling' => 'Cycling', 'Sports' => 'Sports']
            ]
        ];
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
