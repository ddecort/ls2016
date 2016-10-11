<?php namespace wildfire\Dealer\Controllers;

use Backend\Classes\Controller;
use wildfire\Dealer\Models\Zone;
use BackendMenu;
use Flash;

class Zones extends Controller
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
        BackendMenu::setContext('wildfire.Dealer', 'main-menu-item', 'zones');
    }

}
