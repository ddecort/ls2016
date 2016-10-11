<?php namespace wildfire\Product\Models;

use Model;

/**
 * Model
 */
class Colorway extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'wildfire_product_colorway';

    public $belongsTo = [
        'product' => 'wildfire\Product\Models\Product'
    ];

    public $hasMany = [
        'variants' => 'wildfire\Product\Models\Variant'
    ];

    public $belongsToMany = [
        'colors' => [ 'wildfire\Product\Models\Color',
            'table' => 'wildfire_product_colorway_color',
            'order' => 'nest_left asc'
        ]
    ];

    public $loadedcolors = [];
    public $color_list = '';

    public function getColorlistAttribute()
    {
        $ret = implode('/', $this->colors->lists('name'));
        $this->color_list = $ret;

        return $ret;
    }
    public function getColorlistBreakableAttribute()
    {
        $ret = implode(' + ', $this->colors->lists('name'));

        return $ret;
    }

}
