<?php namespace wildfire\Snippet\Components;

use Request;
use Cms\Classes\ComponentBase;
use wildfire\Snippet\Models\Page as Pagemodel;
use wildfire\Snippet\Models\Snippet;
use Redirect;
use Flash;

class Experiencecontent extends ComponentBase
{
    public $pagemodel;
    public $section;
    public $snippets;

    public function componentDetails()
    {
        return [
            'name'        => 'Experience Content', 
            'description' => 'Output the snippets for the experience page based on current sport'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        list($section, $sport) = Pagemodel::findSectionAndSport(Request::path(), $this->page, true);

        $this->section = $this->loadSection($section);
        $this->pagemodel = $this->page['pagemodel'] = $this->loadPagemodel();

        if (!$this->pagemodel && $this->param('sport'))
        {
            \Flash::error('Page Not Found! You have been redirected to the home page.');
            return Redirect::to('/');
        }

        $this->loadCategories();

        $this->snippets = $this->page['snippets'] = $this->loadSnippets();        
    }

    public function loadSection($section)
    {
        return Pagemodel::where('name', 'ilike', $section.' Updates')->first();
    }

    public function loadPagemodel()
    {
        $search_sport = $this->page['sport'] ? $this->page['sport']['slug'] : $this->page['section']['slug'];
        $query = Pagemodel::with([
            'snippets' => function($model) {
                $model->withPivot('sort_order')->orderBy('pivot_sort_order', 'asc');
            },
            'snippets.slides' => function($model){
                $model->orderBy('sort_order','asc');
            },
            ]);

        if ($this->page['sport']) 
        { 
            $pagemodel = $query->where('parent_id', $this->section->id)->where('slug', $search_sport)->first();
            if ($pagemodel) $this->page->title = "Lizard Skins ".str_replace(' Updates','',$pagemodel->name).' | Experience';
        }
        else
        {
            $pagemodel = $query->where('id',$this->section->id)->first();
            if ($pagemodel) $this->page->title = "Lizard Skins ".str_replace(' Homepage','',$this->page['section']['name']). ' | Experience';
        }

        return $pagemodel;
    }

    public function loadCategories()
    {
        if ($this->pagemodel->isLeaf()){
            $cats = $this->pagemodel->parent()->first()->children;
        } else {
            $cats = $this->pagemodel->children;
        }

        $this->page['subcategories'] = $cats;
    }

    public function loadSnippets()
    {
        $snippets = [];
        $pg = $this->pagemodel;
        $snips = Snippet::whereNull('parent_snippet_id')->whereHas('pages', function($query) use ($pg){
                                $query->where('nest_left', '>=', $pg->nest_left)->where('nest_right', '<=', $pg->nest_right);
                 })->orderBy('created_at','desc')->with('slides')->take(20)->get();
        foreach ($snips AS $snip)
        {
            if ($snip->type == 'slideshow')
            {
                $this->addJs('/plugins/wildfire/snippet/assets/jquery.flexslider.min.js');
                $this->addCss('/plugins/wildfire/snippet/assets/jquery.flexslider.css');
            }
        }

        return $snips;
    }


}
