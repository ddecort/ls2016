<?php namespace wildfire\Product\Models;

use Model;

/**
 * Model
 */
class Size extends Model
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
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wildfire_product_size';

    public $hasMany = [
        'variants' => 'wildfire\Product\Models\Variant'
    ];


    public function getGenerateShortNameAttribute()
    {
        if ($this->shortname)
        {
            return $this->shortname;
        }
        else
        {
            return $this->name;
        }
    }
}
