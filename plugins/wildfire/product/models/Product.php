<?php namespace wildfire\Product\Models;

use Model;
use Db;


/**
 * Model
 */
class Product extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required'
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wildfire_product_product';

    public $hasMany = [
        'colorways' => [
            'wildfire\Product\Models\Colorway',
        ],
        'images' => [
            'wildfire\Product\Models\Image',
            'order' => 'sort_order asc'
        ],
        'variants' => [
            'wildfire\Product\Models\Variant',
            'order' => 'sort_order asc',
            'delete' => true
        ]
    ];

    public $hasManyThrough = [
        'colors' => [ 'wildfire\Product\Models\Color',
            'through' => 'wildfire\Product\Models\Colorway'
        ]
    ];

    public $belongsToMany = [
        'categories' => ['wildfire\Product\Models\Category',
            'table' => 'wildfire_product_in_category'
        ]
    ];

    public $loaded_colorways = [];
    public $chosen_colorway = false;
    public $context_category = null;
    public $total_adds = 0;

    protected $guarded = [];

    public function scopeFilterCategory($query, $categories)
    {
        $allcats = array();
        foreach ($categories AS $cat)
        {
            $cat = Category::find($cat);
            $allcats = array_merge($allcats, $cat->getAllChildrenAndSelf()->lists('id'));
        }

        return $query->whereHas('categories', function($q) use ($allcats) {
                $q->whereIn('id', $allcats);
        });
    }

    public function getPreviewImageAttribute()
    {
        $img = false;
        if ($this->chosen_colorway)
        {
            foreach ($this->images AS $thisimg)
            {
                if ($thisimg->colorway_id == $this->chosen_colorway){
                    return $thisimg->image;
                }
            }
        }
        foreach ($this->images AS $thisimg)
        {
            if ($thisimg->colorway_id === null && $thisimg->size_id === null)
            {
                return $thisimg->image;
            }
        }
        if ($this->images && count($this->images) > 0)
        {
            return $this->images[0]->image;
        }

        return false;
    }

    public function getNameDisplayAttribute()
    {
        $out = str_replace(array('_', '-'), array('&nbsp;', '&#x2011;'), htmlspecialchars($this->name));
        $out = preg_replace('/ - /', '<span class="brk">'."\n".'</span><span class="hyp"> -&nbsp;</span>', $out);

        return $out;
    }

    public function getNamePlainAttribute()
    {
        return str_replace('_', '&nbsp;', $this->name);
    }

    public function getNameTextAttribute()
    { 
        return str_replace('_', ' ', $this->name);
    }

    public function getDefaultUrlAttribute()
    {
        return $this->defaultUrl();
    }

    public function defaultUrl($within = null)
    {
        if ($this->context_category)
        {
            $cats = [];
            foreach ($this->categories AS $cat)
            {
                if ($cat->nest_left >= $this->context_category['nest_left'] && $cat->nest_right <= $this->context_category['nest_right'])
                {
                    $cats[] = $cat;
                }
            }
            if (count($cats) == 0)
            {
                $cats = $this->categories()->get();
            }
        }
        else
        {
            $cats = $this->categories()->get();
        }
        $min = 500;
        $min_cat = null;
        foreach ($cats AS $cat)
        {
            $depth = $cat->getDepth();
            if ($depth < $min)
            {
                $min = $depth;
                $min_cat = $cat;
            }
        }
        $ret = $min_cat->defaultUrl . '/' . $this->slug;
        return $ret;
    }

    public function getPriceinfoAttribute()
    {
        $range_min = 1000000;
        $range_max = 0;
        foreach ($this->variants AS $variant)
        {
            $price = round($variant->price);
            if ($price < $range_min) $range_min = $variant->price;
            if ($price > $range_max) $range_max = $variant->price;
        }

        if (round(100*$range_min) == round(100*$range_max))
        {
            return '$'.number_format($range_min, 2);
        }
        //else return '$'.number_format($range_min, 2) .' - $'. number_format($range_max, 2);
        else return ('From $'.number_format($range_min, 2));
    }

    public function getColorchoicesAttribute()
    {
        $choices = ['hasdouble' => false, 'hastriple' => false, 'hasthumbs' => false, 'colorways' => [], 'colors' => []];
        $image_lu = [];
        foreach ($this->images AS $image)
        {
            if ($image->colorway_id !== null && $image->colorway_id && !isset($image_lu[$image->colorway_id]))
            {
                $image_lu[$image->colorway_id] = $image; 
            }
        }

        foreach ($this->variants AS $variant)
        {
            
            if ($variant->colorway_id && !isset($choices['colorways'][$variant->colorway_id]))
            {
                $colors = $variant->colorway->colors;
                $choices['hastriple'] = ($choices['hastriple'] || count($colors) == 3);
                $choices['hasdouble'] = ($choices['hasdouble'] || count($colors) == 2);
                $colorsf = [];
                foreach ($colors AS $color)
                {
                    $colorsf[] = $color;
                }
                $thumb = (isset($image_lu[$variant->colorway_id]) ? $image_lu[$variant->colorway_id] : false);
                $choices['colorways'][$variant->colorway_id] = ['colorlist' => $variant->colorway->colorlistBreakable, 'colorway' => $variant->colorway->toArray(), 'colors' => $colorsf, 'thumb' => $thumb ? $thumb->image : $thumb];
            }             
        }
        $choices['colorways'] = array_values($choices['colorways']);

        if (count($choices['colorways']) == count($image_lu))
        {
            $choices['hasthumbs'] = true;
        }

        return $choices;
    }

    public function getSizechoicesAttribute()
    {
        $choices = array();
        $size_ids = [];
        foreach ($this->variants as $var)
        {
            if ($var->size_id !== null && !isset($choices[$var->size_id]))
            {
                $choices[$var->size_id] = $var->size;
            }
        }

        return array_values($choices);
    }

    public function beforeDelete()
    {
        $id = $this->id;
        Db::table('wildfire_product_in_category')->where('product_id',$id)->delete();
    }
}
