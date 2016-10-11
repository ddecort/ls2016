<?php namespace wildfire\Snippet\Controllers;

use Backend\Classes\Controller;
use wildfire\Snippet\Models\Snippet;
use BackendMenu;
use Backend;
use Session;
use Flash;
use Db;

class Snippets extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController',
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';
    public $type = '';
    public $updateid = '';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('wildfire.Snippet', 'main-menu-item', 'snippets');

        // Grab the drag and drop requirements
        $this->addCss('/plugins/bedard/dragdrop/assets/css/sortable.css');
        $this->addJs('/plugins/bedard/dragdrop/assets/js/html5sortable.js');
        $this->addJs('/plugins/bedard/dragdrop/assets/js/sortable.js');
        
    }

    public function update($recordId, $context = null)
    {

        $obj = Snippet::find($recordId);
        $this->updateid = $recordId;
        $this->type = $obj ? $obj->type : 'meow';

        if ($frm = get('frm'))
        {
            Session::put('fwd_snippet_'.$obj->id, [$frm, get('fid')]);
        }


        // Call the FormController behavior update() method
        return $this->asExtension('FormController')->update($recordId, $context);

    }

    //redirect back to most appropriate page after edit
    public function formAfterUpdate($model)
    {
        $url = '';
        if ($frm = Session::get('fwd_snippet_'.$model->id))
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
        else if ($model->parent_snippet_id)
        {
            $url = 'snippet/snippets/update/'.$model->parent_snippet_id;
        }
        else
        {
            if ($page = $model->pages()->first())
            {
                $url = 'snippet/pages/update/'.$page->id;
            }
            else if ($cat = $model->categories()->first())
            {
                $url = 'product/categories/update/'.$cat->id;
            }
            else
            {
                $url = 'snippet/snippets';
            }
        }
        $url = explode('/', $url);
        for ($i = 1; $i <= 4; $i++ )
        {
            $model->{'fwd'.$i} = isset($url[$i - 1]) ? $url[$i - 1] : '';
        }
    }

    public function formExtendFields($form)
    {
        if (get('add_to_page'))
        {
            $field = $form->getField('pages');
            if (!$field->value) $field->value = [];
            $field->value[] = get('add_to_page');
            Session::put('fwd_snippet_'.$this->updateid, ['page', get('add_to_page')]);
        }
        if (get('add_to_category'))
        {
            $field = $form->getField('categories');
            if (!$field->value) $field->value = [];
            $field->value[] = get('add_to_category');
            Session::put('fwd_snippet_'.$this->updateid, ['category', get('add_to_category')]);
        }
        if (get('add_to_snippet'))
        {
            $field = $form->getField('parent_snippet_id');
            $field->value = get('add_to_snippet');
            Session::put('fwd_snippet_'.$this->updateid, ['snippet', get('add_to_snippet')]);
        }
        if (get('add_to_athlete'))
        {
            $field = $form->getField('athletes');
            if (!$field->value) $field->value = [];
            $field->value[] = get('add_to_athlete');
            Session::put('fwd_snippet_'.$this->updateid, ['athlete', get('add_to_athlete')]);
        }
    }

    public function listExtendQuery($query)
    {
        $query->where('parent_snippet_id',null);
    }

     public function update_onUpdateSlidesPosition()
    {
        $model = Snippet::find(post('model_id'));

        $moved = [];
        $position = 0;
        if (($reorderIds = post('checked')) && is_array($reorderIds) && count($reorderIds)) {
            foreach ($reorderIds as $id) {
                if (in_array($id, $moved) || !$record = Snippet::find($id))
                    continue;
                $record->sort_order = $position;
                $record->save();
                $moved[] = $id;
                $position++;
            }
            Flash::success('Successfully re-ordered slides.');
        }

        $this->initForm($model);
        $this->initRelation($model, 'slides');

        return $this->relationRefresh('slides');
    }

    public function update_onUpdateProductPosition()
    {
        $model = Snippet::find(post('model_id'));

        $moved = [];
        $position = 0;
        if (($reorderIds = post('checked')) && is_array($reorderIds) && count($reorderIds)) {
            foreach ($reorderIds as $id) {
                if (in_array($id, $moved))
                    continue;
                Db::table('wildfire_snippet_snippet_product')->where('snippet_id', $model->id)->where('product_id', $id)->update(['sort_order' => $position]);
                $moved[] = $id;
                $position++;
            }
            Flash::success('Successfully re-ordered products.');
        }

        $this->initForm($model);
        $this->initRelation($model, 'products');

        return $this->relationRefresh('products');
    }

    public function index_onDelete()
    {
        if (($checkedIds = post('checked')) && is_array($checkedIds) && count($checkedIds)) {

            foreach ($checkedIds as $snippetId) {
                if ((!$snippet = Snippet::find($snippetId)))
                    continue;

                $snippet->delete();
            }

            Flash::success('Successfully deleted those categories.');
        }

        return $this->listRefresh();
    }



}
