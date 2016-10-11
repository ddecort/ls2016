<?php namespace wildfire\Snippet\Components;

use Db;
use App;
use Request;
use Redirect;
use Cache;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use wildfire\Product\Models\Product;
use wildfire\Product\Models\Category;
use wildfire\Product\Models\Color;
use wildfire\Product\Models\Colorway;
use wildfire\Snippet\Models\Page as Pagemodel;

class Categorycontent extends ComponentBase
{

    public $section;
    public $section_arr;
    public $sport;
    public $sport_arr;
    public $category;
    public $category_arr;
    public $category_arr_nokids;
    public $parents;
    public $product = false;
    public $products;
    public $related;
    public $snippets;
    public $has_list_snippet = false;

    public function componentDetails()
    {
        return [
            'name'        => 'Category Page',
            'description' => 'List of products and snippets'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->addJs('/plugins/wildfire/snippet/assets/select2.min.js');
        $this->addCss('/plugins/wildfire/snippet/assets/select2.min.css');

        list($section, $sport) = Pagemodel::findSectionAndSport(Request::path(), $this->page, true);

        $this->section_arr = $this->loadSection();
        if (!$this->section_arr){
            \Flash::error('Our website has recently changed, and the page you requested couldn\'t be found. Please choose from the links below to find what you\'re looking for.');
            return Redirect::to('/');
        }
        $this->sport_arr = $final_cat = $interim_cat = $this->loadSport();
        if (!$this->sport_arr)
        {
            \Flash::error('Our website has recently changed, and the page you requested couldn\'t be found. Please choose from the \'Shop\' menu above or content below to find what you\'re looking for.');
            return Redirect::to('/'.$this->section_arr['slug']);
        }

        $parents = [];
        $input_cats = explode('/', $this->param('cats'));
        $used_cats = [$this->sport_arr['slug']];

        foreach ($input_cats AS $cat)
        {
            if ($cat != '' && $cat != 'all')
            {
                $new_cat = false;
                foreach ($interim_cat['children'] AS $child){
                    if ($child['slug'] == $cat){
                        $new_cat = $child;
                        break;
                    }
                }
                if ($new_cat)
                {
                    if ($interim_cat['id'] != $this->section_arr['id'])
                    {
                        $parents[] = $interim_cat;
                    }
                    $used_cats[] = $cat;
                    $final_cat = $interim_cat = $new_cat;
                }
                else
                {
                    $product = Product::whereHas('categories', function($query) use ($interim_cat){
                        $query->where('id',$interim_cat['id']);
                    })->where('slug',$cat)->with('variants.size','variants.colorway.colors.big_swatch','variants.colorway.colors.small_swatch','colorways.colors','images.image','images.rotation_images')->first();
                    if ($product)
                    {
                        $parent = count($parents) > 0 ? $parents[count($parents)-1] : null;
                        if ($interim_cat['id'] != $this->section_arr['id'])
                        {
                            $parents[] = $interim_cat;
                        }

                        $gripbuilder = false;
                        $allcats = $product->categories()->get();
                        foreach ($allcats AS $allcat)
                        {
                            if (strpos(strtolower($allcat->name),'lock') !== false)
                            {
                                $gripbuilder = true;
                            }
                            if (strpos(strtolower($product->name),'skill') !== false)
                            {
                                $gripbuilder = false;
                            }
                        }

                        $this->category_arr = $interim_cat;
                        $this->category_arr['parent'] = $parent;
                        $this->product = $this->page['product'] = $product;
                        $this->addJs('/plugins/wildfire/snippet/assets/uber-zoom.js');
                        $this->addJs('/plugins/wildfire/snippet/assets/product.js');
                        $this->addCss('/plugins/wildfire/snippet/assets/product.css');

                        if ($gripbuilder)
                        {
                            // spinny thing
                            $this->addJs('/themes/ls2016/assets/gripbuilder/jquery.reel-min.js');

                            // font files
                            $this->addJs('/themes/ls2016/assets/gripbuilder/cufon-yui.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/agencyfb.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/army_wide.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/conspiracy.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/dk_babysitter.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/museo.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/patinio_basica.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/wolfsbane.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/alba.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/bank_gothic.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/creampuff.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/eccentric.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/komika_axix.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/olde_english.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/pulp_fiction.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/alba_super.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/brody.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/crillee.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/elliot.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/lithos.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/outage.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/pussycat.font.js');
                            $this->addJS('/themes/ls2016/assets/gripbuilder/anastasia.font.js'); 
                            $this->addJS('/themes/ls2016/assets/gripbuilder/college.font.js');  
                            $this->addJS('/themes/ls2016/assets/gripbuilder/esp.font.js'); 
                            $this->addJS('/themes/ls2016/assets/gripbuilder/montserrat.font.js'); 
                            $this->addJS('/themes/ls2016/assets/gripbuilder/padaloma_italic.font.js'); 
                            $this->addJS('/themes/ls2016/assets/gripbuilder/weltron.font.js');
                        }

                        $this->page['gripbuilder'] = $gripbuilder;
                        $this->related = $this->page['related'] = $this->loadRelatedProducts();

                        $plug = Product::where('name','Custom Plug Color')->with('variants', 'images')->first();
                        $this->page['plug_variant'] = $plug->variants[0];

                        $engrave = Product::where('name','Custom Ring Engraving')->with('variants','images')->first();
                        $this->page['engrave_variant'] = $engrave->variants[0];
                    }
                    else
                    {

                        \Flash::error('Our website has recently changed, and the page you requested couldn\'t be found. Please choose from the \'Shop\' menu above or content below to find what you\'re looking for.');
                        return Redirect::to('/'.implode('/',$used_cats));
                    }
                }        
            }
            else
            {
                break;
            }
        }
        
        $this->page->title = 'Lizard Skins '.$this->sport_arr['name'].' | '. ($this->product ? $this->product->nameText : $final_cat['name']);

        $this->parents  = $this->page['parents']  = $parents;
        $this->category_arr_nokids = $this->category_arr = $this->page['category'] = $final_cat; 

        //turn the cache-loaded arrays into workable objects for further querying in templates
        unset($this->section_arr['children'],$this->sport_arr['children'], $this->category_arr_nokids['children']);
        $this->section = $this->page['section'] = Category::make($this->section_arr);
        $this->sport = $this->page['sport'] = Category::make($this->sport_arr);
        $this->category = $this->page['category'] = Category::make($this->category_arr_nokids);

        $prods = $this->listProducts();
        $this->products = $this->page['products'] = $prods['all'];
        if ((count($this->products) == 1) && !$this->product)
        {
            $prod = current($this->products);
            //only one product!
            return Redirect::to($prod->defaultUrl);
        }
        $this->snippets = $this->page['snippets'] = $this->loadSnippets();
        $this->has_list_snippet = $this->page['has_list_snippet'] = $this->checkListSnippet();

        if (count($prods['filtered']))
        {
            $this->products = $this->page['products'] = $prods['filtered'];
        }
    }

    public function onCartTrack()
    {
        Db::table('wildfire_product_variant')->where('id',post('variant_id'))->increment('cart_adds');
    }

    public function loadSection()
    {
        $cats = Category::cachedTree();
        $category = false;
        foreach ($cats AS $cat)
        {
            if (strtolower($cat['slug']) == strtolower($this->page['section']['slug']))
            {
                $category = $cat;
            }
        }

        return $category;
    }

    public function loadSport()
    {
        $sport = false;
        foreach ($this->section_arr['children'] AS $cat)
        {
            if ($cat['slug'] == $this->page['sport']['slug'])
            {
                $sport = $cat;
            }
        }
        return ($sport ? $sport : $this->section_arr);
    }

    public function loadSnippets()
    {
        $snippets = [];
        if ($this->product)
        {
            $query = $this->category->snippets()->with(['categories' => function($query) { $query->where('for_product',true); }]);
        }
        else
        {
            $query = $this->category->snippets()->with(['categories' => function($query) { $query->where('for_category',true); }]);
        }
        $snips = $query->with('slides.image_desktop','slides.image_mobile','image_desktop','image_mobile')->orderBy('pivot_sort_order','asc')->get();
        foreach ($snips AS $snippet)
        {
            if ($snippet->type == 'category_filter')
            {
                if ($snippet->filter_color && $this->products)
                {
                    $colors = [];
                    foreach ($this->products AS $product)
                    {
                        foreach ($product->colorways AS $colorway)
                        {
                            foreach ($colorway->colors AS $color)
                            {
                                $colors[$color->id] = $color;
                            }
                        }
                    }
                    $lvl1_colors = [];
                    foreach ($colors AS $color)
                    {
                        if ($color->nest_depth == 2)
                        {
                            $color = $color->parent()->first();
                        }
                        $lvl1_colors[$color->id] = $color;

                    }
                    $final_colors = [];
                    foreach ($lvl1_colors AS $color)
                    {
                        $final_colors[$color->nest_left] = $color;
                    }
                    ksort($final_colors);
                    $this->page['allcolors'] = $final_colors;
                }
                if ($snippet->filter_subcategory)
                {
                    $subcats = [];
                    if ($this->category_arr['nest_left'] + 1 == $this->category_arr['nest_right'])
                    {
                        $root = $this->parents[count($this->parents) - 1];
                    }
                    else
                    {
                        $root = $this->category_arr;
                    }
                    $root_nokids = $root;
                    unset($root_nokids['children']);
                    $subcats['root'] = Category::make($root_nokids);
                    $subcats['rest'] = [];
                    foreach ($root['children'] AS $child)
                    {
                        $subcats['rest'][] = Category::make($child);
                    }

                    $this->page['subcategories'] = $subcats;
                }
            }
            else if ($snippet->type == 'slideshow')
            {
                $this->addJs('/plugins/wildfire/snippet/assets/jquery.flexslider.min.js');
                $this->addCss('/plugins/wildfire/snippet/assets/jquery.flexslider.css');
            }


            if ($this->product && $snippet->pivot->for_product) $snippets[] = $snippet;
            if (!$this->product && $snippet->pivot->for_category) $snippets[] = $snippet;
        }
        return $snippets;
    }

    public function checkListSnippet()
    {
        foreach ($this->snippets AS $snippet)
        {
            if ($snippet->type == 'category_list')
            {
                return true;
            }
        }

        return false;
    }

    public function listProducts()
    {
        if (!$this->category_arr)
            return null;

        $fc = null; //filter color
        $ft = null; //filter category

        if (get('fc'))
        {
            $this->fc = $this->page['fc'] = get('fc');
            $fc = Color::find(get('fc'));
        }

        $collect = ['all' => [], 'filtered' => []];
        $cats = $this->getChildCategoryIds($this->category_arr, null, true);
        $prod_ids = Db::table('wildfire_product_category')
            ->select('wildfire_product_in_category.product_id')
            ->join('wildfire_product_in_category','wildfire_product_category.id','=','wildfire_product_in_category.category_id')
            ->where('nest_left','>=',$this->category_arr['nest_left'])
            ->where('nest_right','<=',$this->category_arr['nest_right'])
            ->orderBy('wildfire_product_category.nest_left','asc')
            ->orderBy('wildfire_product_in_category.sort_order','asc')
            ->lists('product_id');
        $sorted_keys = [];
        foreach ($prod_ids AS $prod_id)
        {
            $sorted_keys[$prod_id] = null;
        }
        $prods = Product::whereIn('id',array_keys($sorted_keys))->with('categories','variants.size','images.image','colorways.colors.small_swatch')->get();
        foreach ($prods AS $prod)
        {
            if ($prod->active) 
            {
                $sorted_keys[$prod->id] = $prod;
            }
            else 
            {
                unset($sorted_keys[$prod->id]);
            }
        }
        foreach ($sorted_keys AS $pid => $prod)
        {
            if (!is_object($prod)){
                var_dump($pid);
                var_dump($prod);
                var_dump($sorted_keys);

                die();
            }
            $prod->context_category = $this->category;
            if (!isset($collect['all'][$prod->id]))
            {
                if ($fc)
                {
                    foreach ($prod->colorways AS $colorway)
                    {
                        foreach ($colorway->colors AS $color)
                        {
                            if ($color->nest_left >= $fc->nest_left && $color->nest_right <= $fc->nest_right)
                            {
                                $prod->chosen_colorway = $colorway->id;
                                $collect['all'][$prod->id] = $prod;
                                $collect['filtered'][$prod->id] = $prod;
                                break(2);
                            }
                        }
                    }
                }
                $collect['all'][$prod->id] = $prod;
            }
        }

        return $collect;
    }

    public function loadRelatedProducts()
    {
        $limit = 8;
        $total = 0;
        $related = ['thiscat' => [], 'othercats' => []];

        //get other products in category
        $cat = ($this->category_arr ? $this->category_arr['id'] : 0);
        $products = Product::where('id','<>',$this->product->id)->where('active', true)->whereHas('categories', function($query) use ($cat){
            $query->where('id',$cat);
        })->with('categories','variants','images.image','colorways','colorways.colors')->take(8)->get();
        foreach ($products AS $product)
        {
            if (!isset($related['thiscat'][$product->id]))
            {
                $product->context_category = $this->category_arr;
                $related['thiscat'][$product->id] = $product;
                $total ++;
            }
        }

        //get products from other categories this thing is in

        if ($total < 4 && $this->category_arr)
        {
            $cats = $this->product->categories()->lists('id');
            if (count($cats) > 1)
            {
                $products = Product::where('id','<>',$this->product->id)->where('active', true)->whereHas('categories', function($query) use ($cats, $cat){
                    $query->whereIn('id',$cats)->where('id','!=',$cat);
                })->with('categories','variants','images.image','colorways','colorways.colors')->take(8)->get();
                foreach ($products AS $product)
                {
                    if (!isset($related['othercats'][$product->id]) && !isset($related['thiscat'][$product->id]))
                    {
                        $product->context_category = $this->category_arr;
                        $related['othercats'][$product->id] = $product;
                        $total ++;
                    }
                }
            }
            if ($total < 4 && $this->category_arr)
            {
                $parent = $this->category_arr['parent'];
                $morecats = $this->getChildCategoryIds($parent, $this->category_arr['id'], true);

                //get other products in sport
                $existing_ids = array_keys($related['thiscat']) + array_keys($related['othercats']);
                $existing_ids[] = $this->product->id;
                $products = Product::whereNotIn('id',$existing_ids)->where('active', true)->whereHas('categories', function($query) use ($morecats){
                    $query->whereIn('id',$morecats);
                })->with('categories','variants','images.image','colorways', 'colorways.colors')->take(8 - count($related))->get();
                foreach ($products AS $product)
                {
                    $product->context_category = $this->category_arr;
                    $related['othercats'][$product->id] = $product;
                }
            }
        }

        return $related;
    }

    public function getChildCategoryIds($parent, $except = null, $and_parent = false)
    {
        $ret = [];
        if ($and_parent) $ret[] = $parent['id'];

        if (count($parent['children']) > 0)
        {
            foreach ($parent['children'] AS $child)
            {
                if ($child['id'] != $except)
                {
                    $ret[] = $child['id'];
                    if (count($child['children']) > 0)
                    {
                        $ret = array_merge($ret, $this->getChildCategoryIds($child, $except));
                    }
                }
            }
        }
        return $ret;
    }
    public function getChildCategoriesFlat($parent, $and_parent = false)
    {
        $ret = [];
        if ($and_parent) $ret[] = $parent;

        foreach ($parent['children'] AS $child)
        {
            $ret[] = $child;
            if (count($child['children']) > 0)
            {
                $ret = array_merge($ret, $this->getChildCategoriesFlat($child, $except));
            }
        }
        return $ret;
    }

}

