<?php namespace wildfire\Dealer\Models;

use wildfire\Product\Models\Category;
use Backend\Models\ImportModel;
use ApplicationException;

/**
 * Dealer Import Model
 */
class DealerImport extends ImportModel
{
    public $table = 'wildfire_dealer_dealers';

    /**
     * Validation rules
     */
    public $rules = [
        'name' => 'required'
    ];

    protected $categoryNameCache = [];

    protected $zoneNameCache = [];

    public function getDealerCategoriesOptions()
    {
        return Category::lists('name', 'id');
    }

    public function importData($results, $sessionKey = null)
    {
        $firstRow = reset($results);

        /*
         * Import
         */
        foreach ($results as $row => $data) {
            try {

                if (!$title = array_get($data, 'name')) {
                    $this->logSkipped($row, 'Missing dealer name');
                    continue;
                }

                /*
                 * Find or create
                 */
                $dealer = Dealer::make();
        		$dupe = $this->findDuplicateDealer($data);
                $dealer = $dupe ?: $dealer;
                $dealerExists = $dealer->exists;

                if (!$dupe || $this->update_existing) {
                    $changed = false;
                    /*
                     * Set attributes
                    */
                    $except = ['id', 'categories', 'zone'];

                    foreach (array_except($data, $except) as $attribute => $value) {
                        if ($dealer->{$attribute} != ($value ?: null)){
                            $dealer->{$attribute} = $value ?: null;
                                         $changed = true;
                        }
                    }

                    $dealer->forceSave();

                    if ($categoryIds = $this->getCategoryIdsForPost($data)) {
                        $dealer->categories()->sync($categoryIds, false);
                   }

                    if ($zone = $this->getZoneForPost($data)) {
                        $dealer->zone()->associate($zone);
                    }


                    /*
                     * Log results
                     */
                    if ($dealerExists) {
                        if ($changed){
                            $this->logUpdated();
                        }
                    }
                    else {
                        $this->logCreated();
                    }
                }
            }
            catch (Exception $ex) {
                $this->logError($row, $ex->getMessage());
            }
        }
    }

    protected function findDuplicateDealer($data)
    {
        $name = trim(array_get($data, 'name'));
        $dealer = Dealer::where('name', $name);

        if ($city = trim(array_get($data, 'city'))) {
            $dealer->where('city', $city);
        }
	    if ($phone = trim(array_get($data, 'phone'))) {
	        $dealer->where('phone', $phone);
	    }

        return $dealer->first();
    }

    protected function getCategoryIdsForPost($data)
    {
        $ids = [];

        if ($this->dealer_categories) {
            $ids = (array) $this->dealer_categories;
        }

        return $ids;
    }

    protected function getZoneForPost($data)
    {
        $id = null;

        $name = trim(array_get($data,'zone'));

        if (isset($this->zoneNameCache[$name])) {
            $id = $this->zoneNameCache[$name];
        }
        else {
            if ($this->auto_create_zones) {
                $newZone = Zone::firstOrCreate(['name' => $name]);
                $id = $this->zoneNameCache[$name] = $newZone;
            } else {
                if (!is_null($newZone = Zone::firstByAttributes(['name' => $name]))) {
                    $id = $this->zoneNameCache[$name] = $newZone;
                }
            }
        }

        return $id;
    }



}
