<?php namespace wildfire\Athlete\Controllers;

use wildfire\Product\Models\Category;
use wildfire\Athlete\Models\Athlete;
use Backend\Classes\Controller;
use Flash;
use BackendMenu;

class Athletes extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
    ]; 

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('wildfire.Athlete', 'main-menu-item', 'athletes');

        // Grab the drag and drop requirements
        $this->addCss('/plugins/bedard/dragdrop/assets/css/sortable.css');
        $this->addJs('/plugins/bedard/dragdrop/assets/js/html5sortable.js');
        $this->addJs('/plugins/bedard/dragdrop/assets/js/sortable.js');        
    }

    public function listExtendQuery($query, $definition = null)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function listFilterExtendQuery($query, $scope)
    {
        return $query->forAthlete();
    }

    public function create()
    {
        BackendMenu::setContextSideMenu('new_athlete');
        return $this->asExtension('FormController')->create();
    }

    public function index_onUpdatePosition()
    {
        $moved = [];
        $position = 0;
        $cycling_cat = Category::where('slug','cycling')->first();
        if (($reorderIds = post('checked')) && is_array($reorderIds) && count($reorderIds)) {
            foreach ($reorderIds as $id) {
                if (in_array($id, $moved) || !$record = Athlete::find($id))
                    continue;
                if ($record->categories()->where('nest_left','>=',$cycling_cat->nest_left)->where('nest_right','<=',$cycling_cat->nest_right)->count() > 0 )
                {
                    $record->sort_order = 100 + $position;
                }
                else
                {
                    $record->sort_order = $position;
                }
                $record->save();
                $moved[] = $id;
                $position++;
            }
            Flash::success('Successfully re-ordered records.');
        }
        return $this->listRefresh();
    }

    public function convert()
    {
        $athletes = Athlete::with('sports')->get();
        foreach ($athletes AS $athlete)
        {
            echo "<br />".$athlete->name.': <ul>';
            foreach ($athlete->sports AS $sport)
            {
                echo '<li>'.$sport->name.' ';
                if ($sport->name == 'Mountain Bike') $name = 'MTB';
                else if ($sport->name == 'Bat Wrap') $name = 'Bat Grip';
                else $name = $sport->name;
                $find = Category::where('name', $name)->get();
                if (count($find) > 1)
                {
                    echo '<span style="color: #aaaa00;">more than one</span>';
                }
                else if (count($find) == 1)
                {
                    echo '<span style="color: #00ff00;"> found cat= '.$find[0]->id.' = '.$find[0]->name.'</span>';
                    $athlete->categories()->attach($find[0]->id);
                }
                else
                {
                    echo '<span style="color: #ff0000;"> not found!</span>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        die();
    }

}
