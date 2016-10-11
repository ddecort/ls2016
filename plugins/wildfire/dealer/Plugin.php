<?php namespace wildfire\Dealer;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'wildfire\Dealer\Components\Dealerlist' => 'dealerlist',
            'wildfire\Dealer\Components\DealersLink' => 'dealersLink',
            'wildfire\Dealer\Components\DealersForm' => 'dealersForm',
        ];
    }
}
