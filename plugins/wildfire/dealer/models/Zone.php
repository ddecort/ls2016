<?php namespace wildfire\Dealer\Models;

use Model;

/**
 * Model
 */
class Zone extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\NestedTree;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wildfire_dealer_zone';

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

    public $hasMany = [
        'dealers' => 'wildfire\Dealer\Models\Dealer',
    ];

    public function afterDelete()
    {
        $this->dealers()->detach();
    }

    public function getDealerCountAttribute()
    {
        return $this->dealers()->count();
    }


}
