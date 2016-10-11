<?php namespace wildfire\Athlete;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
               'wildfire\Athlete\Components\Athletelist' => 'athletelist',
               'wildfire\Athlete\Components\AthletesLink' => 'athleteslink',
               'wildfire\Athlete\Components\Athleteshow' => 'athleteshow',
        ];
    }

    public function registerSettings()
    {
    }
}
