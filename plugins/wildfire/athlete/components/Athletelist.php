<?php namespace wildfire\Athlete\Components;

use Request;
use Cms\Classes\ComponentBase;
use wildfire\Athlete\Models\Athlete;
use wildfire\Product\Models\Category;
use wildfire\Snippet\Models\Page as Pagemodel;

class Athletelist extends ComponentBase
{

    public $athletes;
    public $subcategories;
    public $category;

    public function componentDetails()
    {
        return [
            'name'        => 'Athlete Listing', 
            'description' => 'List of athletes, filterable by sport'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        list($section, $sport) = Pagemodel::findSectionAndSport(Request::path(), $this->page, true);

        $this->category = $this->page['category'] = $this->loadCategory();
        $this->subcategories = $this->page['subcategories'] = $this->loadSubcategories();
        $this->athletes = $this->page['athletes'] = $this->listAthletes();

        $this->page['prefix'] = '/'.($sport ? $sport : $section);

        $this->page->title = 'Lizard Skins '.$this->category->name.' | Athletes';

        $this->addJs('/plugins/wildfire/snippet/assets/packery.pkgd.min.js');
    }

    public function loadCategory()
    {
        return Category::forAthleteFilter()->where('slug',$this->page['section']['slug'])->first();
    }

    public function loadSubcategories()
    {
        if (!$this->category) 
        {
            return Category::forAthleteFilter()->allChildren();
        }
        else
        {
            return $this->category->allChildren()->forAthleteFilter()->get();
        } 
    }

    public function listAthletes()
    {
        if (!$this->category)
            return null;

        $cats = $this->category->getAllChildrenAndSelf()->lists('id');
        $athletes = Athlete::whereHas('categories', function($q) use ($cats) {
            $q->whereIn('id', $cats);
        })->orderBy('sort_order','asc')->get();

        return $athletes;
    }
}
