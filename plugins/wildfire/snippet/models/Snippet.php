<?php namespace wildfire\Snippet\Models;

use Model;
use Instagram\Instagram;
use Metaphorceps\Instagram\Models\Settings;
use Wildfire\Snippet\Models\Page as PageModel;
use Wildfire\Product\Models\Product;
use Wildfire\Product\Models\Category;
use Db;
use \Cache;
use \Request;
use \Session;
use \Carbon\Carbon;

/**
 * Model
 */
class Snippet extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = true;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wildfire_snippet_snippet';

    public $hasMany = [
        'slides' => ['wildfire\Snippet\Models\Snippet', 
            'table' => 'wildfire_snippet_snippet', 
            'key' => 'parent_snippet_id',
            'order' => 'sort_order asc'
        ],
        'columns' => ['wildfire\Snippet\Models\Snippet', 
            'table' => 'wildfire_snippet_snippet', 
            'key' => 'parent_snippet_id',
            'order' => 'sort_order asc'
        ]
    ];

    public $belongsToMany = [
        'pages' => ['wildfire\Snippet\Models\Page', 
            'table' => 'wildfire_snippet_snippet_page', 
            'pivot' => ['sort_order']
        ],
        'products' => ['wildfire\Product\Models\Product', 
            'table' => 'wildfire_snippet_snippet_product', 
            'pivot' => ['sort_order']
        ],
        'categories' => ['wildfire\Product\Models\Category', 
            'table' => 'wildfire_snippet_snippet_category', 
            'pivot' => ['sort_order','for_category', 'for_product'],
            'scope' => 'forProduct'
        ],
        'athletes' => ['wildfire\Athlete\Models\Athlete', 
            'table' => 'wildfire_snippet_snippet_athlete', 
            'pivot' => ['sort_order'],
            'order' => 'wildfire_athlete_athlete.sort_order asc'
        ]
    ];

    public $belongsTo = [
        'feeds' => ['wildfire\Snippet\Models\Page',
            'key' => 'feed_category_id'
        ]
    ];

    public $attachOne = [
        'image_desktop' => 'System\Models\File',
        'image_mobile' => 'System\Models\File'
    ];

    public $attachMany = [
        'content_slideshow' => 'System\Models\File'
    ];

    protected $guarded = [];

    public function getTypeOptions(){
        return [
            'slide' => 'Single <em style="color: #696; display:inline-block; padding-left: 10px;">(photo or video or blog post)</em>',
            'slideshow' => 'Slider <em style="color: #696; display:inline-block; padding-left: 10px;">(animated slider with multiple photos)',
            'products_info' => 'Products <em style="color: #696; display:inline-block; padding-left: 10px;">(product grid using products chosen manually)</em>',
            'content' => 'Text/HTML <em style="color: #696; display:inline-block; padding-left: 10px;">(text/html block)</em>',
            'instagram' => 'Instagram <em style="color: #696; display:inline-block; padding-left: 10px;">(latest from an account, filter by hashtag)</em>',
            'feed' => 'Feed <em style="color: #696; display:inline-block; padding-left: 10px;">(grab latest snippets from another page like blog or homepage)</em>',
            'split_row' => 'Split-Row <em style="color: #696; display:inline-block; padding-left: 10px;">(layout 2-4 snippet types horizontally along a row)</em>',
            'category_list' => 'Category Products <em style="color: #696; display:inline-block; padding-left: 10px;">(displays products from current category page)</em>',
            'category_filter' => 'Category Filter <em style="color: #696; display:inline-block; padding-left: 10px;">(filter category products by color or sub-category)</em>',
            'products_related' => 'Related Products <em style="color: #696; display:inline-block; padding-left: 10px;">(ranked by similarity to current product)</em>',
            'bestsellers' => 'Best Selling Products <em style="color: #696; display:inline-block; padding-left: 10px;">(contextual, based on sport)</em>',
            'company_menu' => 'Submenu <em style="color: #696; display: inline-block; padding-left: 10px;">(For use on company pages)</em>'
        ];
    }    

    public function getInstagramUserOptions()
    {
        return [
            'lizardskinsbaseball' => 'lizardskinsbaseball',
            'lizardskinslacrosse' => 'lizardskinslacrosse',
            'lizardskinscycling'  => 'lizardskinscycling'
        ];
    }

    public function toArray()
    {
        $result = parent::toArray();
        foreach ($result AS $key => $val)
        {
            if ($key == 'pivot')
            {
                foreach ($result[$key] AS $pivot_key => $pivot_col)
                {
                    $result[$key.'_'.$pivot_key] = $pivot_col;
                }
            }
        }

        return $result;
    }

    public function getVideoiframeUrlAttribute()
    {
        if ($this->is_video && $this->link_url)
        {
            if ($this->video_iframe)
            {
                return $this->video_iframe;
            }
            else
            {
                $url = $this->link_url;
            
                // youtube
                $pattern = "/(?:youtube(?:-nocookie)?\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)(?<id>[a-zA-Z0-9_-]{11})/i";
                preg_match( $pattern, $url, $matches );
                if ($matches && $matches['id']) {
                    $this->video_iframe = '<iframe type="text/html" src="https://www.youtube.com/embed/'.$matches['id'].'?autoplay=1&modestbranding=1&rel=0&showinfo=0" frameborder="0" allowfullscreen>';
                    $this->save();
                }

                //vimeo
                if (strpos($url, 'vimeo') !== false)
                {
                    $data = json_decode(file_get_contents('https://vimeo.com/api/oembed.json?url='.$url.'&autoplay=true&byline=false&portrait=false&title=false'));
                    if ($data->html) 
                    {
                        $this->video_iframe = $data->html;
                        $this->save();
                    }
                }

                return $this->video_iframe;
            }
        }
    }

    public function beforeSave()
    {
        if (get('add_to_snippet') && !$this->parent_snippet_id)
        {
            $this->parent_snippet_id = (int) get('add_to_snippet');
        }
        if ($this->aspect_ratio_split && $this->type == 'split_row')
        {
            $this->aspect_ratio = $this->aspect_ratio_split;
        }
        unset($this->aspect_ratio_split);
        unset($this->attributes['aspect_ratio_split']);

        if ($this->type == 'slide')
        {
            $this->content = $this->content_blog;
        }
        if ($this->type == 'slide')
        {
            $this->content_background = $this->content_blog_background;
        }
        unset($this->content_blog);
        unset($this->attributes['content_blog']);
        unset($this->content_blog_background);
        unset($this->attributes['content_blog_background']);

        if (!$this->created_at)
        {
            $this->created_at = Carbon::now()->toDateString();
        }

        //clear the cache
        $cats = $this->pages()->get();
        foreach ($cats AS $cat){
            Cache::forget('feed_'.$cat->id);
        }
    }

    public function afterFetch()
    {
        $this->setAttribute('aspect_ratio_split', $this->aspect_ratio);
        $this->setAttribute('content_blog', $this->content);
        $this->setAttribute('content_blog_background', $this->content_background);
    }

    public function getFeedResultsAttribute()
    {
        $cat_id = $this->feed_category_id;
        $snippets = Cache::remember('feed_'.$cat_id, 30, function() use ($cat_id){
            $out = [];
            $found = [];
            if ($cat = PageModel::find($cat_id))
            {
                $found = Snippet::where('type','slide')->whereNull('parent_snippet_id')->whereHas('pages', function($query) use ($cat){
                    $query->where('nest_left', '>=', $cat->nest_left)->where('nest_right', '<=', $cat->nest_right);
                })->orderBy('created_at','desc')->take(10)->get();
            }
            foreach ($found AS $find)
            {
                $out[$find->id] = $find->toArray();
            }

            return $out;
        });

        $inserts = [];
        $used = Session::get('used_feed_snippet_ids',[]);
        $key = substr($_SERVER['REQUEST_TIME'],-5);
        $pageused = Session::get('used_feed_snippet_page_ids_'.$key, []);
        while (count($inserts) < $this->feed_number && count($inserts) != count($snippets))
        {
            $avail_keys = array_keys($snippets);
            $can_use_keys = array_diff($avail_keys, $used);

            if (count($can_use_keys) == 0)
            {
                $used = $pageused;
                $can_use_keys = array_diff($avail_keys, $pageused);
                if (count($can_use_keys) == 0)
                {
                    $used = [];
                    $can_use_keys = $avail_keys;
                }
            }

            $rand = array_rand($can_use_keys);
            $snip_id = $can_use_keys[$rand];
            $used[] = $snip_id;
            $pageused[] = $snip_id;
            $snip = $snippets[$snip_id];
            $toinsert  = Snippet::make($snip);
            $toinsert->exists = true;
            $inserts[] = $toinsert;
        }
        Session::put('used_feed_snippet_ids', $used);
        Session::put('used_feed_snippet_page_ids_'.$key, $pageused);

        return $inserts;
    }

    public function getInstagramDetailsAttribute()
    {
        $posts = InstagramPost::getPosts($this->instagram_user,$this->instagram_hashtag,$this->instagram_post_id);
        return InstagramPost::pickUnusedPost($posts);
    }

    public function getBestsellerDetailsAttribute($passed_category = null, $passed_product = null)
    {
        $output = [];
        if ($passed_category)
        {
            $flatcats = Category::cachedList();
            $category = $flatcats[$passed_category->parent_id];
            while ($category['nest_depth'] > 1){
                $category = $flatcats[$category['parent_id']];
            }

            $prod_ids = Cache::remember('bestsellers_'.$category['id'], 30, function() use ($category){
                $ids = Db::table('wildfire_product_product')
                    ->select(Db::raw('sum(wildfire_product_variant.cart_adds) as adds, wildfire_product_product.id'))
                    ->join('wildfire_product_variant','wildfire_product_product.id','=','wildfire_product_variant.product_id')
                    ->join('wildfire_product_in_category', 'wildfire_product_product.id','=','wildfire_product_in_category.product_id')
                    ->join('wildfire_product_category', 'wildfire_product_in_category.category_id','=','wildfire_product_category.id')
                    ->where('wildfire_product_product.active',true)
                    ->where('wildfire_product_category.nest_left','>=',$category['nest_left'])
                    ->where('wildfire_product_category.nest_right','<=',$category['nest_right'])
                    ->groupBy('wildfire_product_product.id');
                $ids = $ids->orderBy('adds','desc')->take(10)->lists('id');
                return $ids;
            });

            $sorted = [];
            $prods = Product::whereIn('id',$prod_ids)->with('categories','variants','images.image','colorways')->get();
            foreach ($prods AS $prod)
            {
                if ($passed_product && $passed_product->id == $prod->id)
                {
                    continue;
                }
                if ($passed_category)
                {
                    $prod->context_category = $category;
                }


                $max_adds = 0;
                $total_adds = 0;
                foreach ($prod->variants AS $var)
                {
                    $total_adds += $var->cart_adds;
                    if ($var->cart_adds > $max_adds)
                    {
                        $max_adds = $var->cart_adds;
                        $prod->chosen_colorway = $var->colorway_id;
                    }
                }
                $prod->total_adds = $total_adds;
                $output[$prod->id] = $prod;
            }

            foreach ($prod_ids AS $id){
                if (isset($output[$id]))
                {
                    $sorted[] = $output[$id];
                }
            }
        }
        return $sorted;
    }

    public function getNormalizedContentBackgroundAttribute()
    {
        return '#'.str_replace('#','',$this->content_background);
    }
}
