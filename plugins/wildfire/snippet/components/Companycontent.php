<?php namespace wildfire\Snippet\Components;

use App;
use Request;
use Cms\Classes\ComponentBase;
use wildfire\Snippet\Models\Page as Pagemodel;
use wildfire\Snippet\Models\Snippet;
use Redirect;
use Flash;

class Companycontent extends ComponentBase
{
    public $pagemodel;
    public $snippets;
    public $slug;
    public $companyroot;

    public function componentDetails()
    {
        return [
            'name'        => 'Company Content', 
            'description' => 'Output the snippets for a particular company page'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        list($section, $sport) = Pagemodel::findSectionAndSport(Request::path(), $this->page, true);

        $this->slug = $this->page['slug'] = $this->param('slug');
        $this->companyroot = $this->page['companyroot'] = $this->loadCompanyroot();
        $this->pagemodel = $this->page['pagemodel'] = $this->loadPagemodel();

        $this->companymenu = $this->page['companymenu'] = $this->loadCompanymenu();
    
        if (!$this->pagemodel && $this->param('slug'))
        {
            \Flash::error('Page Not Found! You have been redirected to the home page.');
            return Redirect::to('/');
        }

        $this->snippets = $this->page['snippets'] = $this->loadSnippets();        
    }

    public function loadCompanyroot()
    {
        return Pagemodel::where('parent_id', $this->page['section']['id'])->where('slug', 'company')->with(['snippets' => function($model){
            $model->withPivot('sort_order')->orderBy('pivot_sort_order', 'asc');
        }])->first();
    }

    public function loadCompanymenu()
    {
        $ret = [];
        $ret[] = $this->companyroot;
        foreach ($this->companyroot->children()->get() as $cat)
        {
            $ret[] = $cat;
        }

        return $ret;
    }

    public function loadPagemodel()
    {
        $company_root = $this->companyroot;
        $search_slug = $this->slug ? $this->slug : $this->page['section']['slug'];
        $query = Pagemodel::with([
            'snippets' => function($model) {
                $model->withPivot('sort_order')->orderBy('pivot_sort_order', 'asc');
            },
            'snippets.slides' => function($model){
                $model->orderBy('sort_order','asc');
            },
            ]);

        if ($this->param('slug'))
        { 
            $pagemodel = $query->where('parent_id', $company_root->id)->where('slug', $search_slug)->first();
            if ($pagemodel) $this->page->title = "Lizard Skins ".str_replace(' Homepage','',$this->page['section']['name']) . ' | '.explode(' ',$pagemodel->name)[0];
        }
        else
        {
            $pagemodel = $company_root; 
            if ($pagemodel) $this->page->title = "Lizard Skins ".str_replace(' Homepage','',$this->page['section']['name']) . ' | About';
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
