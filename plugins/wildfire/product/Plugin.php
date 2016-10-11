<?php namespace wildfire\Product;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'wildfire\Product\Components\Shopmenu' => 'shopmenu',
            'wildfire\Product\Components\Cart' => 'cart'
        ];
    }

    public function registerSettings()
    {
    }
}
