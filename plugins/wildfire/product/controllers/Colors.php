<?php namespace wildfire\Product\Controllers;

use Backend\Classes\Controller;
use wildfire\Product\Models\Color;
use BackendMenu;

class Colors extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ReorderController'
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('wildfire.Product', 'main-menu-item', 'colors');
    }

}
