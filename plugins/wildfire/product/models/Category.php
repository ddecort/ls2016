<?php namespace wildfire\Product\Models;

use Model;
use Cache;

/**
 * Model
 */
class Category extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\NestedTree;

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required',
        'slug' => ['required', 'regex:/^[a-z0-9\/\:_\-\*\[\]\+\?\|]*$/i']
    ];

    public $guarded = [];
    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wildfire_product_category';

    public $belongsToMany = [
        'products' => ['wildfire\Product\Models\Product',
            'table' => 'wildfire_product_in_category',
            'pivot' => 'sort_order'
        ],
        'snippets' => ['wildfire\Snippet\Models\Snippet',
            'table' => 'wildfire_snippet_snippet_category',
            'pivot' => ['sort_order','for_category','for_product']
        ]
    
    ];


    public static function cachedTree()
    {
        $cats = Cache::rememberForever('categories_tree', function(){
            return Category::getNested()->toArray();
        });
        return $cats;
    }

    //NOT TESTED
    public static function cachedDescendants($arr)
    {
        $cats = self::cachedTree();
        return self::findNode($cats, $arr);
    }

    //NOT TESTED
    public static function findNode($tree, $arr)
    {
        foreach ($tree AS $node)
        {
            if ($node['id'] == $arr['id'])
            {
                return $node;
            }
            if ($node['nest_left'] < $arr['nest_left'] && $node['nest_right'] > $arr['nest_right'])
            {
                return self::findNode($node, $arr);
            }
        }
        return null;
    }

    public static function cachedList()
    {
        $cats = Cache::rememberForever('categories_list', function(){
            $out = [];
            $cs = Category::get();
            foreach ($cs AS $c)
            {
                $out[$c->id] = $c->toArray();
            }
            return $out;
        });
        return $cats;
    }

    public static function cachedUrls()
    {
        $urls = Cache::rememberForever('categories_urls', function() {
            $urls = [];
            foreach (Category::cachedTree() AS $site)
            {
                $urls = $urls + Category::getUrls($site,'');
            }
            return $urls;
        });
        return $urls;
    }

    public static function getUrls($cat,$prefix)
    {
        $urls = [];
        foreach ($cat['children'] AS $child)
        {
            if ($child['slug'] == 'accessories' && $cat['nest_depth'] == 0)
            {
                $prefix = '/'.$cat['slug'];
            }
            $urls[$child['id']] = $prefix . '/' . $child['slug'];
            if ($child['children'])
            {
                $urls = $urls + self::getUrls($child,$prefix.'/'.$child['slug']);
            }
        }
        return $urls;
    }

    public function scopeForDealer($query)
    {
        return $query->where('use_for_dealers', true);
    }

    public function scopeForAthlete($query)
    {
        return $query->where('use_for_athletes', true);
    }

    public function scopeForAthleteFilter($query)
    {
        return $query->where('use_for_athletes_filter', true);
    }

    public function scopeForProduct($query)
    {
        return $query->where('use_for_products_filter', true);
    }

    public function afterDelete()
    {
        Cache::forget('categories');
        $this->products()->detach();
    }

    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }

    public function getIndentedNameAttribute()
    {
        return str_repeat('—',(max(0,$this->getDepth()))).$this->name;
    }

    public function getSubmenucategoriesAttribute()
    {
        $res = $this->children()->forProduct()->get();
        $more = $this->parent()->first()->children()->where('slug','accessories')->first();
        foreach ($more->getChildren() AS $acc)
        {
            $res[] = $acc;
        }
        return $res;
    }

    public function getDefaultUrlAttribute()
    {
        $lu = $this->cachedUrls();
        return (isset($lu[$this->id]) ? $lu[$this->id] : '/');
    }

    public function afterSave()
    {
        Cache::forget('categories');
    }

}
