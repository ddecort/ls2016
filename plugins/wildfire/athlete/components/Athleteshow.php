<?php namespace wildfire\Athlete\Components;

use Db;
use App;
use Request;
use Flash;
use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use wildfire\Athlete\Models\Athlete;
use wildfire\Product\Models\Category as Sport;

class Athleteshow extends ComponentBase
{

    public $athlete;
    public $sports;

    public function componentDetails()
    {
        return [
            'name'        => 'Athlete Detail', 
            'description' => 'Show a single Athlete'
        ];
    }

    public function defineProperties()
    {
        return [
            'id' => [
                'title' => 'ID',
                'description' => 'The athlete ID. Get it from the page url.',
                'type' => 'string',
                'default' => '{{ :id }}'
            ]
        ];
    }

    public function onRun()
    {
        $this->athlete = $this->page['athlete'] = $this->loadAthlete();

        if (!$this->athlete)
        {
            Flash::error('Our website has recently changed, and the page you requested couldn\'t be found. Please choose from the links below to find what you\'re looking for.');
            return Redirect::to('/'.$this->param('section').'/athletes');
        }

        $this->sports = $this->page['sports'] = $this->loadSports();

        $this->addJs('/plugins/wildfire/snippet/assets/jquery.flexslider.min.js');
        $this->addCss('/plugins/wildfire/snippet/assets/jquery.flexslider.css');        
    }

    public function loadAthlete()
    {
        $aid = $this->property('id');
        $athlete = null;
        if (is_numeric($aid))
        {
            $athlete = Athlete::find($aid);
            $this->page->title = 'Lizard Skins Athletes | '.$athlete->name;
        }

        return $athlete;
    }

    public function loadSports()
    {
        if (!$this->athlete){
            return null; 
        }
        else if (!$this->athlete->categories()){
            return null;
        }
        else 
        {
            $topcats = array();
            foreach ($this->athlete->categories()->get() AS $cat)
            {
                $is_child = false;
                foreach ($topcats AS $topcat)
                {
                    if ($cat->isInsideSubtree($topcat)) $is_child = true;
                }
                if (!$is_child && $cat->nest_depth > 0)
                {
                    $topcats[] = $cat;
                }
            }
            $ret = array();
            foreach ($topcats AS $topcat)
            {
                $ret[] = $topcat->name;
            }
            return implode(', ', $ret);
        }
    }
}
