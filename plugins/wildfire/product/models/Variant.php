<?php namespace wildfire\Product\Models;

use wildfire\Product\Models\Colorway;
use Model;


/**
 * Model
 */
class Variant extends Model
{
    use \October\Rain\Database\Traits\Validation;

    private $clear_colors = false;
    private $loopcount = 0;
    private $colorway_set = false;

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
    public $table = 'wildfire_product_variant';

    public $belongsTo = [
        'product' => 'wildfire\Product\Models\Product',
        'size' => 'wildfire\Product\Models\Size',
        'colorway' => 'wildfire\Product\Models\Colorway',
    ];

    public $belongsToMany = [
        'colors' => [ 'wildfire\Product\Models\Color',
            'table' => 'wildfire_product_variant_has_color',
            'order' => 'nest_left asc'
        ]
    ];
    public function beforeSave()
    {
        if (!$this->colorway_set && $this->temp_colorway)
        {
            if ($this->temp_colorway == 'new' || !is_numeric($this->temp_colorway))
            {
                $this->colorway_id = null;
            }
            else
            {
                $this->colorway_id = Colorway::find($this->temp_colorway) ? $this->temp_colorway : null;
            }
        }

        unset($this->temp_colorway);
        unset($this->attributes['temp_colorway']);
    }

    public function afterSave()
    {
        $this->loopcount++;
        if (!$this->colorway_id && $this->colors()->count() > 0)
        {
            if ($this->loopcount > 5)
            {
                die();
            }
            $found = false;
            $existings = $this->product->colorways()->get();
            $my_ids = $this->colors()->lists('id');
            foreach ($existings AS $existing)
            {
                $existing_ids = $existing->colors()->lists('id');
                if ($existing_ids == $my_ids)
                {
                    $this->colorway_id = $existing->id;
                    $this->colorway_set = true;
                    $this->save();
                    $this->clear_colors = true;
                    $found = true;
                }
            }
            if (!$found)
            {
                $colorway = new Colorway();
                $colorway->product = $this->product;
                $colorway->save();
                $colorway->colors()->sync($my_ids);

                $this->colorway_id = $colorway->id;
                $this->colorway_set = true;

                $this->save();
                $this->clear_colors = true;
            }
        }
        else if ($this->colorway_id && $this->colors()->count() > 0)
        {
            $this->clear_colors = true;
        }

        if ($this->clear_colors)
        {
            $this->colors()->sync([0]);
            $this->clear_colors = false;
        }
    }

    public function getVariantTitleAttribute()
    {
        $desc = [];
        if ($this->colorway_id){
            $desc[] = $this->colorway->colorList;
        }
        if ($this->size_id){
            $desc[] = $this->size->generateShortName;
        }

        return implode(',',$desc);
    }

    public function afterFetch()
    {
        $this->setAttribute('temp_colorway', $this->colorway_id);
    }

}
