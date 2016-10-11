<?php namespace wildfire\Snippet\Components;

use Request;
use Cms\Classes\ComponentBase;
use wildfire\Snippet\Models\Page as Pagemodel;
use wildfire\Snippet\Models\Snippet;
use Redirect;
use Flash;

class Pagecontent extends ComponentBase
{
    public $pagemodel;
    public $snippets;

    public function componentDetails()
    {
        return [
            'name'        => 'Page Content', 
            'description' => 'Output the snippets for a particular page'
        ];
    }

    public function defineProperties()
    {
        return [];
    }


    public function onRun()
    {
        list($section, $sport) = Pagemodel::findSectionAndSport(Request::path(), $this->page, true);

        $this->pagemodel = $this->page['pagemodel'] = $this->loadPagemodel();

        if (!$this->pagemodel && $this->param('sport'))
        {
            \Flash::error('Page Not Found! You have been redirected to the home page.');
            return Redirect::to('/'.$section);
        }

        $this->page['sport'] = $this->pagemodel;

        $this->snippets = $this->page['snippets'] = $this->loadSnippets();        
    }

    public function loadPagemodel()
    {
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
            $pagemodel = $query->where('id', $this->page['sport']['id'])->first();
            if ($pagemodel) $this->page->title = "Lizard Skins ".str_replace(' Homepage','',$this->page['sport']['name']);
        }
        else
        {
            $pagemodel = $query->where('id',$this->page['section']['id'])->first();
            if ($pagemodel) $this->page->title = "Lizard Skins ".str_replace(' Homepage','',$this->page['section']['name']);
        }

        return $pagemodel;
    }

    public function loadSnippets()
    {
        $snippets = [];

        $snips = $this->pagemodel->snippets()->with('slides','columns')->orderBy('pivot_sort_order','asc')->get();
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
