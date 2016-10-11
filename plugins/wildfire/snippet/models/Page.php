<?php namespace wildfire\Snippet\Models;

use Model;
use Cache;

/**
 * Model
 */
class Page extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\NestedTree;
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
    public $table = 'wildfire_snippet_page';

    public $belongsToMany = [
        'snippets' => ['wildfire\Snippet\Models\Snippet', 
            'table' => 'wildfire_snippet_snippet_page', 
            'pivot' => 'sort_order'
        ]
    ];

    public static function cachedTree()
    {
        $pages = Cache::rememberForever('pages_tree', function(){
            return self::getNested()->toArray();
        });
        return $pages;
    }

    public static function cachedList()
    {
        $pages = Cache::rememberForever('pages_tree', function(){
            $out = [];
            $cs = self::get();
            foreach ($cs AS $c)
            {
                if ($c->nest_depth < 2)
                {
                    $out[$c->id] = $c->toArray();
                }
            }
            return $out;
        });
        return $pages;
    }

    public static function cachedFullList()
    {
        $pages = Cache::rememberForever('pages_full_tree', function(){
            $out = [];
            $cs = self::get();
            foreach ($cs AS $c)
            {
                $out[$c->id] = $c->toArray();
            }
            return $out;
        });
        return $pages;
    }


    public static function findSectionAndSport($url, $pageval = null, $return_slugs = false)
    {
        if ($pageval)
        {
            if (isset($pageval['section']) && isset($pageval['sport']))
            {
                if ($return_slugs)
                {
                    $section = $pageval['section'];
                    $sport = $pageval['sport'];
                    return array(($section ? strtolower($section['slug']) : null), ($sport ? strtolower($sport['slug']) : null));
                }
                else
                {
                    return;
                }
            }
        }
        if (substr($url,0,1) == '/') $url = substr($url, 1);
        $bits = explode('/',$url);
        $first = $bits[0];

        $pages = self::cachedList();
        $section = null;
        $sport = null;
        foreach ($pages AS $page)
        {
            if (strtolower($page['slug']) == strtolower($first))
            {
                if ($page['nest_depth'] == 0)
                {
                    $section = $page;
                }
                else
                {
                    $section = $pages[$page['parent_id']];
                    $sport = $page;
                    break;
                }
            }
        }
        if (!$sport && count($bits) > 1){
            $second = $bits[1];
            foreach ($pages AS $page)
            {
                if (strtolower($page['slug']) == strtolower($second))
                {
                    $sport = $page;
                    break;
                }

            }
        }
        $pageval['section'] = $section;
        $pageval['sport'] = $sport;

        if ($return_slugs)
        {
            return array(($section ? strtolower($section['slug']) : null), ($sport ? strtolower($sport['slug']) : null));
        }
    }

}
