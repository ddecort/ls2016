<?php namespace wildfire\Product\Components;

use App;
use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use wildfire\Product\Models\Product;
use wildfire\Product\Models\Category;
use wildfire\Snippet\Models\Page as Pagemodel;

class Shopmenu extends ComponentBase
{

    public $section;
    public $section_arr;
    public $cycling;
    public $sports;
    public $categories;
    public $location;

    public function componentDetails()
    {
        return [
            'name'        => 'Shop Menu',
            'description' => 'List of categories by sport'
        ];
    }

    public function defineProperties()
    {
        return [
            'menu' => [
                'title' => 'Menu Location',
                'description' => 'Footer or Header',
                'type' => 'dropdown',
                'options' => ['Header' => 'Header', 'Footer' => 'Footer', 'Company' => 'Company', 'Site Section' => 'Site Section']
            ]
           
        ];
    }


    public function onRun()
    {
        list($section, $sport) = Pagemodel::findSectionAndSport(Request::path(), $this->page, true);

        $this->section_arr = $this->loadSection($section);
        $this->cycling = $this->page['cycling'] = $this->makeSubmenus($this->getSubCats('cycling', 'use_for_athletes_filter'),'cycling');
        $this->sports = $this->page['sports'] = $this->makeSubmenus($this->getSubCats('sports', 'use_for_athletes_filter'),'sports');
        $this->location = $this->page['location'] = $this->property('menu');

        $cats = array();
        $sectioncats = $this->getSubCats($this->section_arr['slug'], 'use_for_products_filter');
        foreach ($sectioncats AS $cat)
        {
            $subcats = [];
            foreach ($cat['children'] AS $subcat)
            {
                if ($subcat['use_for_products_filter'])
                {
                    $subcats[] = ['name' => $subcat['name'], 'slug' => $subcat['slug']];
                }
            }
            if ($cat['slug'] == 'accessories')
            {
                $cat['slug'] = $section.'/'.$cat['slug'];
            }
            $cats[] = ['slug' => $cat['slug'], 'name' => $cat['name'], 'subcats' => $subcats];
        }
        $this->categories = $this->page['categories'] = $cats;

        if ($sport)
        {
            $prefix = '/'.$sport;
        }
        else
        {
            $prefix = '/'.($section ? $section : 'cycling');
        }

        $this->page['prefix'] = $prefix;
    }

    public function makeSubmenus($items, $type)
    {
        $out = [];
        $others = $this->getSubCats($type, 'use_for_products_filter');
        $acc = null;
        foreach ($others as $other)
        {
            if ($other['name'] == 'Accessories')
            {
                $acc = $other;
            }
        }
        foreach ($items AS $item)
        {
            $item['submenu'] = [];
            foreach ($item['children'] AS $child)
            {
                if ($child['use_for_products_filter'])
                {
                    $child['url'] = '/'.$item['slug'].'/'.$child['slug'];
                    $item['submenu'][] = $child;
                }
            }
            if ($acc)
            {
                foreach ($acc['children'] AS $child)
                {
                    if ($child['use_for_products_filter'])
                    {
                        $child['url'] = '/'.$type.'/accessories/'.$child['slug'];
                        $item['submenu'][] = $child;
                    }
                }
            }
            $out[] = $item;
        }

        return $out;
    }

    public function getSubCats($name, $filter, $make_objects = false)
    {
        $section = $this->loadSection($name);
        $output = [];
        if ($section)
        {
            foreach ($section['children'] AS $cat)
            {
                if ($cat[$filter])
                {
                    $output[] = ($make_objects ? Category::make($cat) : $cat);
                }
            }
        }
        return $output;
    }

    public function loadSection($treename)
    {
        $cats = Category::cachedTree();
        $category = false;
        foreach ($cats AS $cat)
        {
            if ($cat['slug'] == $treename)
            {
                $category = $cat;
                break;
            }
        }
        return $category;
    }

}

