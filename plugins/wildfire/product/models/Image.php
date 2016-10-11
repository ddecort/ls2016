<?php namespace wildfire\Product\Models;

use Model;

/**
 * Model
 */
class Image extends Model
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
    public $table = 'wildfire_product_image';

    public $belongsTo = [
        'size' => 'wildfire\Product\Models\Size',
        'colorway' => 'wildfire\Product\Models\Colorway',
        'product' => 'wildfire\Product\Models\Product'
    ];

    public $attachOne = [
        'image' => 'System\Models\File'
    ];

    public $attachMany = [
        'rotation_images' => 'System\Models\File'
    ];
}
