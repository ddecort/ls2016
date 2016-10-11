<?php namespace wildfire\Product\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use wildfire\Product\Models\Category;
use wildfire\Snippet\Models\Snippet;
use Flash;
use Db;

class Categories extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ReorderController',
        'Backend\Behaviors\RelationController'
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('wildfire.Product', 'main-menu-item', 'categories');

        // Grab the drag and drop requirements
        $this->addCss('/plugins/bedard/dragdrop/assets/css/sortable.css');
        $this->addJs('/plugins/bedard/dragdrop/assets/js/html5sortable.js');
        $this->addJs('/plugins/bedard/dragdrop/assets/js/sortable.js');        
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $categoryId) {
                if ((!$category = Category::find($categoryId)))
                    continue;

                $category->delete();
            }

            Flash::success('Successfully deleted those categories.');
        }

        return $this->listRefresh();
    }

    public function update_onUpdateSnippetPosition()
    {
        $model = Category::find(post('model_id'));

        $moved = [];
        $position = 0;
        if (($reorderIds = post('checked')) && is_array($reorderIds) && count($reorderIds)) {
            foreach ($reorderIds as $id) {
                if (in_array($id, $moved))
                    continue;
                Db::table('wildfire_snippet_snippet_category')->where('category_id', $model->id)->where('snippet_id', $id)->update(['sort_order' => $position]);
                $moved[] = $id;
                $position++;
            }
            Flash::success('Successfully re-ordered snippets.');
        }

        $this->initForm($model);
        $this->initRelation($model, 'snippets');

        return $this->relationRefresh('snippets');
    }

    public function update_onUpdateProductPosition()
    {
        $model = Category::find(post('model_id'));

        $moved = [];
        $position = 0;
        if (($reorderIds = post('checked')) && is_array($reorderIds) && count($reorderIds)) {
            foreach ($reorderIds as $id) {
                if (in_array($id, $moved))
                    continue;
                Db::table('wildfire_product_in_category')->where('category_id', $model->id)->where('product_id', $id)->update(['sort_order' => $position]);
                $moved[] = $id;
                $position++;
            }
            Flash::success('Successfully re-ordered products.');
        }

        $this->initForm($model);
        $this->initRelation($model, 'products');

        return $this->relationRefresh('products');
    }



}
