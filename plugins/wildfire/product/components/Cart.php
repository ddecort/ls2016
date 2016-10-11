<?php namespace wildfire\Product\Components;

use Request;
use Cms\Classes\ComponentBase;
use wildfire\Snippet\Models\Page as Pagemodel;

class Cart extends ComponentBase
{

    public $section;
    public $section_arr;

    public function componentDetails()
    {
        return [
            'name'        => 'Cart Setup',
            'description' => 'Javascript to set up Shopify slide-out cart based on current section'
        ];
    }

    public function defineProperties()
    {
        return [];
    }


    public function onRun()
    {
        list($section, $sport) = Pagemodel::findSectionAndSport(Request::path(), $this->page, true);
    }

}

