<?php namespace wildfire\Product\Controllers;

use Backend\Classes\Controller;
use wildfire\Product\Models\Product;
use wildfire\Product\Models\Variant;
use wildfire\Product\Models\Image;
use wildfire\Product\Models\Color;
use BackendMenu;
use Session;
use Flash;
use Db;

class Products extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController',
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $updateid = '';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('wildfire.Product', 'main-menu-item', 'side-menu-item');

        // Grab the drag and drop requirements
        $this->addCss('/plugins/bedard/dragdrop/assets/css/sortable.css');
        $this->addJs('/plugins/bedard/dragdrop/assets/js/html5sortable.js');
        $this->addJs('/plugins/bedard/dragdrop/assets/js/sortable.js');
    }

    public function update($recordId, $context = null)
    {
        $obj = Product::find($recordId);
        $this->updateid = $obj->id;
        if ($frm = get('frm'))
        {
            Session::put('fwd_product_'.$obj->id, [$frm, get('fid')]);
        }
        return parent::update($recordId, $context);
    }
   
//redirect back to most appropriate page after edit
    public function formAfterUpdate($model)
    {
        $url = '';
        if ($frm = Session::get('fwd_product_'.$model->id))
        {
            switch ($frm[0]){
            case 'product':
                $url = 'product/products/update';
                break;
            case 'page':
                $url = 'snippet/pages/update';
                break;
            case 'snippet':
                $url = 'snippet/snippets/update';
                break;
            case 'category':
                $url = 'product/categories/update';
                break;
            case 'athlete':
                $url = 'athlete/athletes/update';
                break;
            default:
                $url = 'snippet/snippets';
                break;
            }

            $url .= '/'.$frm[1];
        }
        else
        {
            $url = 'product/products';
        }
        $url = explode('/', $url);
        for ($i = 1; $i <= 4; $i++ )
        {
            $model->{'fwd'.$i} = isset($url[$i - 1]) ? $url[$i - 1] : '';
        }
    }
 

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $productId) {
                if ((!$product = Product::find($productId)))
                    continue;

                $product->delete();
            }

            Flash::success('Successfully deleted those products.');
        }

        return $this->listRefresh();
    }

    public function index_onActivate()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $productId) {
                if ((!$product = Product::find($productId)))
                    continue;

                $product->active = true;
                $product->save();
            }

            Flash::success('Successfully set products to active.');
        }

        return $this->listRefresh();
    }

    public function index_onDeactivate()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $productId) {
                if ((!$product = Product::find($productId)))
                    continue;

                $product->active = false;
                $product->save();
            }

            Flash::success('Successfully set products to inactive.');
        }

        return $this->listRefresh();
    }

    public function update_onUpdateVariantPosition()
    {
        $moved = [];
        $position = 0;
        if (($reorderIds = post('checked')) && is_array($reorderIds) && count($reorderIds)) {
            foreach ($reorderIds as $id) {
                if (in_array($id, $moved) || !$record = Variant::find($id))
                    continue;
                $record->sort_order = $position;
                $record->save();
                $moved[] = $id;
                $position++;
            }
            Flash::success('Successfully re-ordered variants.');
        }

        $model = Product::find(post('model_id'));
        $this->initForm($model);
        $this->initRelation($model, 'variants');

        return $this->relationRefresh('variants');
    }


    public function update_onUpdateImagePosition()
    {
        $moved = [];
        $position = 0;
        if (($reorderIds = post('checked')) && is_array($reorderIds) && count($reorderIds)) {
            foreach ($reorderIds as $id) {
                if (in_array($id, $moved) || !$record = Image::find($id))
                    continue;
                $record->sort_order = $position;
                $record->save();
                $moved[] = $id;
                $position++;
            }
            Flash::success('Successfully re-ordered images.');
        }

        $model = Product::find(post('model_id'));
        $this->initForm($model);
        $this->initRelation($model, 'images');

        return $this->relationRefresh('images');
    }


    public function listExtendQuery($query)
    {
        return $query->with('categories', 'variants', 'variants.colors');
    }

}
