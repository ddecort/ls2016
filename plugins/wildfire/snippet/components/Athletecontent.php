<?php namespace wildfire\Snippet\Components;

use Db;
use App;
use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use wildfire\Athlete\Models\Athlete;
use wildfire\Snippet\Models\Snippet;
use Redirect;
use Flash;

class Athletecontent extends ComponentBase
{
    public $athlete;
    public $site;
    public $id;
    public $snippets;

    public function componentDetails()
    {
        return [
            'name'        => 'Athlete Content', 
            'description' => 'Output the snippets for a particular athlete'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->id = $this->page['id'] = $this->param('id');
        $this->athlete = $this->page['athlete'] = Athlete::with('snippets')->find($this->id);

        if (!$this->athlete && $this->param('id'))
        {
            \Flash::error('Athlete Not Found! You have been redirected to the home page.');
            return Redirect::to('/athletes');
        }

        $this->snippets = $this->page['snippets'] = $this->loadSnippets();        
    }

    public function loadSnippets()
    {
        $snippets = [];

        $snips = $this->athlete->snippets()->take(5)->get();
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
