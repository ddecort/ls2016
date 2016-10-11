<?php namespace wildfire\Product\Models;

use Model;

/**
 * Model
 */
class Color extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\NestedTree;
    use \October\Rain\Database\Traits\Sluggable;

    protected $slugs = ['slug' => 'name'];

    /*
     * Validation
     */
    public $rules = [
        'name' => 'required',
        'hex_code' => 'regex:/^[a-f0-9]{6}$/i'
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wildfire_product_color';

    public $attachOne = [
        'small_swatch' => 'System\Models\File',
        'big_swatch' => 'System\Models\File'
    ];

    public $hasMany = [
        'variants' => 'wildfire\Product\Models\Variant'
    ];

}
