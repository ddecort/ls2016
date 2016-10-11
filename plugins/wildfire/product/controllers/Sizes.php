<?php namespace wildfire\Product\Controllers;

use Backend\Classes\Controller;
use wildfire\Product\Models\Size;
use BackendMenu;
use Flash;

class Sizes extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('wildfire.Product', 'main-menu-item', 'sizes');

        // Grab the drag and drop requirements
        $this->addCss('/plugins/bedard/dragdrop/assets/css/sortable.css');
        $this->addJs('/plugins/bedard/dragdrop/assets/js/html5sortable.js');
        $this->addJs('/plugins/bedard/dragdrop/assets/js/sortable.js');
    }

    public function index_onUpdatePosition()
    {
        $moved = [];
        $position = 0;
        if (($reorderIds = post('checked')) && is_array($reorderIds) && count($reorderIds)) {
            foreach ($reorderIds as $id) {
                if (in_array($id, $moved) || !$record = Size::find($id))
                    continue;
                $record->sort_order = $position;
                $record->save();
                $moved[] = $id;
                $position++;
            }
            Flash::success('Successfully re-ordered records.');
        }
        return $this->listRefresh();
    }


}
