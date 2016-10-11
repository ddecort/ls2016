<?php namespace wildfire\Athlete\Models;

use wildfire\Product\Models\Category;
use Model;
use DB;

/**
 * Model
 */
class Athlete extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;

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
    public $table = 'wildfire_athlete_athlete';

    public $belongsToMany = [
        'categories' => [
            'wildfire\Product\Models\Category',
            'table' => 'wildfire_athlete_in_category',
            'order' => 'name',
            'scope' => 'forAthlete'
        ],
        'snippets' => ['wildfire\Snippet\Models\Snippet',
            'table' => 'wildfire_snippet_snippet_athlete',
            'order' => 'created_at desc'
        ]
    ];

    public $attachOne = [
        'index_image' => 'System\Models\File'
    ];

    public $attachMany = [
        'slideshow' => 'System\Models\File'
    ];


    public function afterFetch() { 
        $sports = $this->categories;

        if (is_array($sports)) {
            $sarr = array();
            foreach ($sports AS $sport)
            {
                $sarr[] = $sport->name;
            }
            $this->name_and_sport = $this->name . ' <em>(' . implode(', ', $sarr).')</em>';
        }
    }


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
     * Allows filtering for specifc sports
     * @param  Illuminate\Query\Builder  $query      QueryBuilder
     * @param  array                     $sports     List of product category (sport) ids
     * @return Illuminate\Query\Builder              QueryBuilder
     */
    public function scopeFilterSports($query, $sports)
    {
        $allsports = array();
        foreach ($sports AS $sport)
        {
            $sport = Category::find($sport);
            $allsports = array_merge($allsports, $sport->getAllChildrenAndSelf()->lists('id'));
        }

        return $query->whereHas('categories', function($q) use ($allsports) {
                $q->whereIn('id', $allsports);
        });
    }

    public function getNameAndSportsAttribute()
    {
        $ret = $this->name;
        if ($sports = $this->categories()->whereNotNull('parent_id')->lists('name')){
            $ret .= ' ('.implode(', ', $sports).')';
        }

        return $ret;
    }

}
