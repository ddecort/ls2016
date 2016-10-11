<?php namespace wildfire\Dealer\Models;

use wildfire\Product\Models\Category;
use Model;
use DB;

/**
 * Model
 */
class Dealer extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $implement = ['RainLab.Location.Behaviors.LocationModel'];

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
    public $timestamps = true;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wildfire_dealer_dealer';


    public $belongsToMany = [
        'categories' => [
            'wildfire\Product\Models\Category',
            'table' => 'wildfire_dealer_in_category',
            'order' => 'name',
            'scope' => 'forDealer'
        ]
    ];

    public $belongsTo = [
        'zone' => 'wildfire\Dealer\Models\Zone'
    ];


    public static $dealerTypeOptions = [ 'Dealer', 'Distributor' ];

    //
    // Scopes
    //

    public function scopeIsPublished($query)
    {
        return $query
            ->whereNotNull('active')
            ->where('active', true);
        ;
    }


    /**
     * Lists posts for the front end
     * @param  array $options Display options
     * @return self
     */
    public function scopeListFrontEnd($query, $options)
    {
        /*
         * Default options
         */
        extract(array_merge([
            'page'       => 1,
            'perPage'    => 30,
            'sort'       => 'created_at',
            'categories' => null,
            'category'   => null,
            'search'     => '',
            'published'  => true
        ], $options));

        $searchableFields = ['title', 'slug', 'excerpt', 'content'];

        if ($published) {
            $query->isPublished();
        }

        /*
         * Sorting
         */
        if (!is_array($sort)) {
            $sort = [$sort];
        }

        foreach ($sort as $_sort) {

            if (in_array($_sort, array_keys(self::$allowedSortingOptions))) {
                $parts = explode(' ', $_sort);
                if (count($parts) < 2) {
                    array_push($parts, 'desc');
                }
                list($sortField, $sortDirection) = $parts;
                if ($sortField == 'random') {
                    $sortField = DB::raw('RAND()');
                }
                $query->orderBy($sortField, $sortDirection);
            }
        }

        /*
         * Search
         */
        $search = trim($search);
        if (strlen($search)) {
            $query->searchWhere($search, $searchableFields);
        }

        /*
         * Categories
         */
        if ($categories !== null) {
            if (!is_array($categories)) $categories = [$categories];
            $query->whereHas('categories', function($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }

        /*
         * Category, including children
         */
        if ($category !== null) {
            $category = Category::find($category);

            $categories = $category->getAllChildrenAndSelf()->lists('id');
            $query->whereHas('categories', function($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }

        return $query->paginate($perPage, $page);
    }

    /**
     * Allows filtering for specifc categories
     * @param  Illuminate\Query\Builder  $query      QueryBuilder
     * @param  array                     $categories List of category ids
     * @return Illuminate\Query\Builder              QueryBuilder
     */
    public function scopeFilterCategories($query, $categories)
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

    /**
     * Allows filtering for specifc zones
     * @param  Illuminate\Query\Builder  $query      QueryBuilder
     * @param  array                     $categories List of category ids
     * @return Illuminate\Query\Builder              QueryBuilder
     */
    public function scopeFilterZones($query, $zones)
    {
        $allzones = array();
        foreach ($zones AS $zone)
        {
            $zone = Zone::find($zone);
                $allzones = array_merge($allzones, $zone->getAllChildrenAndSelf()->lists('id'));
        }

        return $query->whereHas('zones', function($q) use ($allzones) {
            $q->whereIn('id', $allzones);
        });
    }

}
