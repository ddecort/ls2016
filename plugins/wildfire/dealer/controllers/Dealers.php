<?php namespace wildfire\Dealer\Controllers;

use wildfire\Dealer\Models\Dealer;
use wildfire\Product\Models\Category;
use RainLab\Location\Models\Country;
use Backend\Classes\Controller;
use BackendMenu;

class Dealers extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.ImportExportController'
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $importExportConfig = 'config_import_export.yaml';
    public $debug;

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('wildfire.Dealer', 'main-menu-item', 'dealers');
    }

    public function create()
    {
        BackendMenu::setContextSideMenu('new_dealer');
        return $this->asExtension('FormController')->create();
    }


    public function listFilterExtendQuery($query, $scope)
    {
        if ($scope->scopeName == 'category')
        {
            return $query->forDealer();
        }
    }

    public function geocode()
    {
        $limit = 500;
        $dealers = Dealer::whereNull('latitude')->whereNull('pre_geocode')->take($limit)->get();
        $debug = [];
        set_time_limit(0);
        foreach ($dealers AS $dealer)
        {
            usleep(200000);
            $make_address = '';
            if (!$dealer->street || !$dealer->old_country || !$dealer->city)
            {
                $debug[] = 'Dealer '.$dealer->name.' missing information. Skipped.';
                continue;        
            }
            if ($dealer->street) $make_address[] = $dealer->street;
            if ($dealer->city) $make_address[] = $dealer->city;
            if ($dealer->old_state) $make_address[] = $dealer->old_state;
            if ($dealer->old_country) $make_address[] = $dealer->old_country;
            $make_address = implode(' ',$make_address);
            $dealer->pre_geocode = $make_address;
            $url = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=".urlencode($make_address);
            $lat_long = get_object_vars(json_decode(file_get_contents($url)));
            if (count($lat_long['results']) == 0)
            {
                $debug[] = '<span style="color: "#ff0000">Dealer <strong>'.$dealer->name.'</strong> not found via geocode ['.$make_address.']. Skipped.</span>';
                continue;
            }
            $details = $lat_long['results'][0];

            $street_address = '';
            $unit = '';
            foreach ($details->address_components AS $cmp)
            {
                if (in_array('street_number', $cmp->types)) $street_address = $cmp->short_name.' '.$street_address;
                else if (in_array('route', $cmp->types)) $street_address .= $cmp->short_name;
                else if (in_array('subpremise', $cmp->types)) $unit = '#'.$cmp->short_name.' ';
                else if (in_array('neighbourhood', $cmp->types)) $dealer->hood = $cmp->short_name;
                else if (in_array('sublocality', $cmp->types)) $dealer->city = $cmp->short_name; 
                else if (in_array('administrative_area_level_1', $cmp->types)) $dealer->state_code = $cmp->short_name; 
                else if (in_array('country', $cmp->types)) $dealer->country_code = $cmp->short_name; 
                else if (in_array('postal_code', $cmp->types)) $dealer->postal = $cmp->short_name;
            }
            $dealer->street = $unit.$street_address;
            $dealer->full_address = $dealer->street .', '. $dealer->city .', '. $dealer->state_code.', '.$dealer->postal.', '.$dealer->country_code;
            $dealer->latitude = $details->geometry->location->lat;
            $dealer->longitude = $details->geometry->location->lng;
            $dealer->save();

            $debug[] = 'Dealer <strong>'.$dealer->name.'</strong> searched ['.$make_address.'] found ['.$details->formatted_address.'] converted ['.$dealer->full_address.'] @ ('.$dealer->latitude.','.$dealer->longitude.')';
        }
        $this->debug = $debug;
    }

    public function geocode_dist()
    {
        $limit = 500;
        $dealers = Dealer::whereNull('latitude')->whereNull('pre_geocode')->whereNull('street')->whereNull('city')->take($limit)->get();
        $debug = [];
        set_time_limit(0);
        foreach ($dealers AS $dealer)
        {
            usleep(200000);
            $make_address = '';
            if ($dealer->old_state) $make_address[] = $dealer->old_state;
            if ($dealer->old_country) $make_address[] = $dealer->old_country;
            $make_address = implode(' ',$make_address);
            $dealer->pre_geocode = $make_address;
            $url = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=".urlencode($make_address);
            $lat_long = get_object_vars(json_decode(file_get_contents($url)));
            if (count($lat_long['results']) == 0)
            {
                $debug[] = '<span style="color: "#ff0000">Dealer <strong>'.$dealer->name.'</strong> not found via geocode ['.$make_address.']. Skipped.</span>';
                continue;
            }
            $details = $lat_long['results'][0];

            $dealer->latitude = $details->geometry->location->lat;
            $dealer->longitude = $details->geometry->location->lng;
            $dealer->save();

            $debug[] = 'Dealer <strong>'.$dealer->name.'</strong> searched ['.$make_address.'] found @ ('.$dealer->latitude.','.$dealer->longitude.')';
        }
        $this->debug = $debug;
    }


}
