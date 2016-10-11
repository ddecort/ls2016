<?php namespace wildfire\Snippet\Controllers;

use Backend\Classes\Controller;
use wildfire\Snippet\Models\Page;
use wildfire\Snippet\Models\Snippet;
use BackendMenu;
use Flash;
use Db;

class Pages extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\ReorderController',
        'Backend\Behaviors\RelationController'
    ];
    
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('wildfire.Snippet', 'main-menu-item', 'pages');

        // Grab the drag and drop requirements
        $this->addCss('/plugins/bedard/dragdrop/assets/css/sortable.css');
        $this->addJs('/plugins/bedard/dragdrop/assets/js/html5sortable.js');
        $this->addJs('/plugins/bedard/dragdrop/assets/js/sortable.js');
    }

    public function update_onUpdateSnippetPosition()
    {
        $model = Page::find(post('model_id'));

        $moved = [];
        $position = 0;
        if (($reorderIds = post('checked')) && is_array($reorderIds) && count($reorderIds)) {
            foreach ($reorderIds as $id) {
                if (in_array($id, $moved))
                    continue;
                Db::table('wildfire_snippet_snippet_page')->where('page_id', $model->id)->where('snippet_id', $id)->update(['sort_order' => $position]);
                $moved[] = $id;
                $position++;
            }
            Flash::success('Successfully re-ordered snippets.');
        }

        $this->initForm($model);
        $this->initRelation($model, 'snippets');

        return $this->relationRefresh('snippets');
    }

}
