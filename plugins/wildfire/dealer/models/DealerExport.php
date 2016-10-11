<?php namespace wildfire\Dealer\Models;

use Backend\Models\ExportModel;
use ApplicationException;

/**
 * Post Export Model
 */
class DealerExport extends ExportModel
{
    public $table = 'wildfire_dealer_dealers';

    /**
     * @var array Relations
     */

    public $belongsToMany = [
        'dealer_categories' => [
            'wildfire\Dealer\Models\Category',
            'table' => 'wildfire_dealer_dealer_categories',
            'key' => 'dealer_id',
            'otherKey' => 'category_id'
        ]
    ];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = [
        'categories'
    ];

    public function exportData($columns, $sessionKey = null)
    {
        $result = self::make()
            ->with([
                'post_categories'
            ])
            ->get()
            ->toArray()
        ;

        return $result;
    }

    public function getCategoriesAttribute()
    {
        if (!$this->post_categories) return '';
        return $this->encodeArrayValue($this->post_categories->lists('name'));
    }
}
