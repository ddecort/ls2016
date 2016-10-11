<?php namespace wildfire\Dealer\Components;

use Db;
use App;
use Cache;
use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RainLab\Location\Models\Country;
use wildfire\Dealer\Models\Dealer;
use wildfire\Product\Models\Category;
use wildfire\Dealer\Models\Zone;
use wildfire\Snippet\Models\Page as Pagemodel;

class Dealerlist extends ComponentBase
{

    public $dealers;
    public $zone;
    public $category;

    public function componentDetails()
    {
        return [
            'name'        => 'Dealer Listing', 
            'description' => 'List of dealers, filterable by zone and sport'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function getSportOptions()
    {
        return Category::forDealer()->listsNested('name','id');
    }

    public function getDealerTypeOptions()
    {
        return Dealer::$dealerTypeOptions;
    }

    public function onRun()
    {
        list($section, $sport) = Pagemodel::findSectionAndSport(Request::path(), $this->page, true);

        $this->category = $this->page['category'] = $this->loadSport($section, $sport);
        $this->subcats = $this->page['subcats'] = $this->loadSubcategories();
        $this->dealers = $this->page['dealers'] = $this->listDealers();

        $this->page->title = 'Lizard Skins '.$this->category->name.' | Dealers';

        $this->addJs('/themes/common/assets/js/markerclusterer.js');
    }

    public function loadSport($section, $sport)
    {
        $cat = false;
        if ($sport)
        {
            $cat = Category::forDealer()->where('slug',$sport)->first();
            $this->page['sportcat'] = $cat;
        }
        if ($cat)
        {
            return $cat->parent()->first();
        }
        else
        {
            $cat = Category::forDealer()->where('slug',$section)->first();
            return $cat;
        }
    }

    public function loadSubcategories()
    {
        if (!$this->category) {
            return Category::forDealer()->allChildren()->get();
        }
        else
        {
            return $this->category->newQuery()->where('parent_id',$this->category->id)->forDealer()->get();
        }
    }

    public function listDealers()
    {
        if (!$this->category)
        {
            return null;
        }

        $cats = $this->category->getAllChildrenAndSelf()->lists('id');

        if (get('lat') && is_numeric(get('lat'))) $this->page['lat'] = (float) get('lat');
        if (get('lng') && is_numeric(get('lng'))) $this->page['lng'] = (float) get('lng');
        if (get('nm')) $this->page['nm'] = get('nm');
        if (get('cc')) $this->page['cc'] = get('cc');

        return null;
    }

    private function findCountryFromBounds($w,$e,$s,$n)
    {
        $lat = ($s + $n)/2;
        $lng = ($w + $e)/2;

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lng."&key=AIzaSyCoMY6MQBwI3syMg947-QPrUYBZ_zPhBVs";
        $addr = json_decode(file_get_contents($url), true);

        if ($addr['results'])
        {
            foreach ($addr['results'][0]['address_components'] AS $cmp)
            {
                if (in_array('country',$cmp['types']))
                {
                    return $cmp['short_name'];
                }
            }
        }

        return null;

    }

    private function getDealerInfoBlock($dealer)
    {
        $info = '<h3>'.$dealer->name.'</h3>';
        if ($dealer->distance_from_search && !$dealer->distributor){
            $info .= '<p class="distance">'.round($dealer->distance_from_search,1).' miles'.(post('frm_name') ? ' from '.post('frm_name') : '').'</p>';
        }
        $info .= '<p>';
        if ($dealer->street) $info .= $dealer->street.'<br />';
        if ($dealer->city) $info .= $dealer->city;
        if ($dealer->state_code) {
            $info .= ($dealer->city ? ', ' : '');
            if (is_numeric($dealer->state_code)) $info .= $dealer->state->name;
            else $info .= $dealer->state_code;
        }
        if ($dealer->postal_code) $info .= '&nbsp;&nbsp;'.$dealer->postal_code;
        if ($dealer->country) $info .= '<br />'.$dealer->country->name;
        $info .= '</p><div>';
        if ($dealer->phone) $info .= '<a href="tel:'.$dealer->phone.'">'.$dealer->phone.'</a>';
        $info .= '</div><div>';
        if ($dealer->email) $info .= '<a href="mailto:'.$dealer->email.'">'.(strlen($dealer->email) > 26 ? 'Send Email' : $dealer->email).'</a>';                
        $info .= '</div><div>';
        if ($dealer->website) $info .= '<a class="website" href="'.(strpos($dealer->website, 'http') !== false ? $dealer->website : 'http://'.$dealer->website).'" target="_blank">'.( strlen($dealer->website) > 26 ? 'View Website' : $dealer->website).'</a>';
        $info .= '</div>';

        return $info;
    }

    private function distance($lat1, $lon1, $lat2, $lon2, $unit) {

      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $unit = strtoupper($unit);

      if ($unit == "K") {
        return ($miles * 1.609344);
      } else if ($unit == "N") {
          return ($miles * 0.8684);
        } else {
            return $miles;
          }
    }

    public function onLoadMarkers()
    {
        $ret = ['dl' => [], 'ds' => []];

        if ($cat = Category::find(post('cat')))
        {
            $cat_ids = $cat->getAllChildrenAndSelf()->where('use_for_dealers',true)->lists('id');
        }
        else
        {
            $cat_ids = null;
        }

        if (post('do_na')){
            return Cache::remember('na_dealers_'.implode('_',$cat_ids),1440, function() use ($cat_ids){
                $ret = ['dl' => [], 'ds' => []];

                $query = Dealer::whereHas('categories', function ($query) use ($cat_ids) {
                    $query->whereIn('id', $cat_ids);
                });

                $query->where(function($query) {
                    $query->where('country_id',2)->orWhere(function($query){
                        $query->where('country_id',1)->where('distributor',false);
                    });//US = 1, Canada = 2
                });
                $dealers = $query->get();

                foreach ($dealers AS $dealer)
                {
                    
                    $d = [
                        'latitude' => ($dealer->distributor ? $dealer->latitude + (rand(-5,5)/10) : $dealer->latitude),
                        'longitude' => ($dealer->distributor ? $dealer->longitude + (rand(-5,5)/10) : $dealer->longitude),
                        'title' => $dealer->name,
                        'infowindow' => $this->getDealerInfoBlock($dealer),
                        'cc' => $dealer->country_code,
                        'q' => '0'
                    ];
                    $ret[($dealer->distributor ? 'ds' : 'dl')][] = $d;
                }

                $ret['cc'] = 'US';

                return $ret;
            });
        }
        else if (post('w') && post('e') && post('n') && post('s'))
        {
            $w = post('w');
            $e = post('e');
            $n = post('n');
            $s = post('s');

            $doexpand = true;

            //find country code
            if (post('frm_cc')){
                $cc = post('frm_cc');
            } else { 
                $cc = $this->findCountryFromBounds($w,$e,$s,$n);
            }

            $query = Dealer::whereBetween('latitude',[$s,$n]);
            if ($cc_obj = Country::where('code',$cc)->first())
            {
                if ($cc == 'CA' || $cc == 'MX')
                {
                    $query->where('country_id',$cc_obj->id);
                }
            }

            //limit to current map view
            if (($w > 0 && $e < 0) || ($w > $e)) {
                //overlapping date line
                $query->where(function($query) use ($w,$e) { 
                    $query->where('longitude','>',$w)->orWhere('longitude','<',$e);
                });
            } else {
                $query->whereBetween('longitude', [$w,$e]);
            }

            //limit to selected category
            if ($cat_ids)
            {
                $query->whereHas('categories', function ($query) use ($cat_ids) {
                    $query->whereIn('id', $cat_ids);
                });
            }

            //calculate distance within DB query for faster result
            if ($frm = post('frm'))
            {
                if ((float) $frm['lat'] < $s || (float) $frm['lat'] > $n || (float) $frm['lng'] < $w || (float) $frm['lng'] > $e)
                {
                    $doexpand = false; //don't re-center map if they've dragged outside the current view!
                }
                $query->select(Db::raw('wildfire_dealer_dealer.*, earth_distance(ll_to_earth( '.(float) $frm['lat'].', '.(float) $frm['lng'].' ), ll_to_earth(wildfire_dealer_dealer.latitude, wildfire_dealer_dealer.longitude))/1609 as distance_from_search'));
                $query->orderBy('distance_from_search','ASC');
            }

            $dealers = $query->get();

            $d_count = 0;
            $dist_count = 0;
            $used_ids = [];
            foreach ($dealers AS $dealer)
            {
                
                $d = [
                    'longitude' => ($dealer->distributor ? $dealer->longitude + $dist_count/20 : $dealer->longitude),
                    'latitude' => $dealer->latitude,
                    'title' => $dealer->name,
                    'infowindow' => $this->getDealerInfoBlock($dealer),
                    'cc' => $dealer->country_code,
                    'q' => 1 
                ];
                if ($dealer->distributor) $dist_count ++;
                $ret[($dealer->distributor ? 'ds' : 'dl')][] = $d;
                $d_count ++;
                $used_ids[$dealer->id] = true;
            }

            //if nothing found in current view, find closest dealers geographically by doing a search (100 miles)
            if ($d_count == 0 && $frm)
            {
                $query = Dealer::whereRaw('earth_box(ll_to_earth('.(float) $frm['lat'].','.(float) $frm['lng'].'), 100000) @> ll_to_earth(wildfire_dealer_dealer.latitude,wildfire_dealer_dealer.longitude)');
                $query->select(Db::raw('wildfire_dealer_dealer.*, earth_distance(ll_to_earth( '.(float) $frm['lat'].', '.(float) $frm['lng'].' ), ll_to_earth(wildfire_dealer_dealer.latitude, wildfire_dealer_dealer.longitude))/1609 as distance_from_search'));
                if ($cc_obj)
                {
                    $query->where(function($query) use ($cc_obj){
                        $query->where(function($query) use ($cc_obj){
                            $query->where('distributor', true)->orWhere(function($query) use ($cc_obj){
                                $query->where('distributor',false)->where('country_id',$cc_obj->id);
                            });
                        });
                    });
                }
                $query->orderBy('distance_from_search','ASC');
                $query->take(50);

                if ($cat_ids)
                {
                    $query->whereHas('categories', function ($query) use ($cat_ids) {
                        $query->whereIn('id', $cat_ids);
                    });
                }

                $dealers = $query->get();

                foreach ($dealers AS $dealer)
                {
                    $d = [
                        'latitude' => $dealer->latitude, 
                        'longitude' => $dealer->longitude, 
                        'title' => $dealer->name,
                        'infowindow' => $this->getDealerInfoBlock($dealer),
                        'expand' => $doexpand,
                        'cc' => $dealer->country_code,
                        'q' => 2
                    ];
                    $ret[($dealer->distributor ? 'ds' : 'dl')][] = $d;
                    $d_count ++;
                    $used_ids[$dealer->id] = true;
                }
            }

            //if nothing found in current view, find closest dealers geographically by doing a search (250 miles)
            if ($d_count == 0 && $frm)
            {
                $query = Dealer::whereRaw('earth_box(ll_to_earth('.(float) $frm['lat'].','.(float) $frm['lng'].'), 250000) @> ll_to_earth(wildfire_dealer_dealer.latitude,wildfire_dealer_dealer.longitude)');
                $query->select(Db::raw('wildfire_dealer_dealer.*, earth_distance(ll_to_earth( '.(float) $frm['lat'].', '.(float) $frm['lng'].' ), ll_to_earth(wildfire_dealer_dealer.latitude, wildfire_dealer_dealer.longitude))/1609 as distance_from_search'));
                if ($cc_obj)
                {
                    $query->where(function($query) use ($cc_obj){
                        $query->where(function($query) use ($cc_obj){
                            $query->where('distributor', true)->orWhere(function($query) use ($cc_obj){
                                $query->where('distributor',false)->where('country_id',$cc_obj->id);
                            });
                        });
                    });
                }
                $query->orderBy('distance_from_search','ASC');
                $query->take(50);

                if ($cat_ids)
                {
                    $query->whereHas('categories', function ($query) use ($cat_ids) {
                        $query->whereIn('id', $cat_ids);
                    });
                }

                $dealers = $query->get();

                foreach ($dealers AS $dealer)
                {
                    $d = [
                        'latitude' => $dealer->latitude, 
                        'longitude' => $dealer->longitude, 
                        'title' => $dealer->name,
                        'infowindow' => $this->getDealerInfoBlock($dealer),
                        'expand' => $doexpand,
                        'cc' => $dealer->country_code,
                        'q' => 3
                    ];
                    $ret[($dealer->distributor ? 'ds' : 'dl')][] = $d;
                    $d_count ++;
                    $used_ids[$dealer->id] = true;
                }
            }


            if ($cc_obj)
            {
                $query = Dealer::where('distributor', true)->where('country_id',$cc_obj->id);

                if ($cat_ids)
                {
                    $query->whereHas('categories', function ($query) use ($cat_ids) {
                        $query->whereIn('id', $cat_ids);
                    });
                }

                if ($frm){
                    $query->select(Db::raw('wildfire_dealer_dealer.*, earth_distance(ll_to_earth( '.(float) $frm['lat'].', '.(float) $frm['lng'].' ), ll_to_earth(wildfire_dealer_dealer.latitude, wildfire_dealer_dealer.longitude))/1609 as distance_from_search'))->orderBy('distance_from_search','ASC');
                }
                $dists = $query->get();

                foreach ($dists AS $dist)
                {
                    if (!array_key_exists($dist->id,$used_ids))
                    {
                        $d = [
                            'latitude' => $dist->latitude, 
                            'longitude' => $dist->longitude, 
                            'title' => $dist->name,
                            'infowindow' => $this->getDealerInfoBlock($dist),
                            'expand' => false,
                            'cc' => $dist->country_code,
                            'q' => 4
                        ];
                        $ret[($dist->distributor ? 'ds' : 'dl')][] = $d;
                    }
                }

                $ret['cc'] = $cc;
            }

            $ret['dragged'] = $doexpand;

            return $ret;
        }
        else
        {
            return false;
        }

    }
}
