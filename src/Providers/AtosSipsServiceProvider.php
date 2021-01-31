<?php

namespace Matthv\AtosSipsGateway\Providers;

use Illuminate\Support\ServiceProvider;

class AtosSipsServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
        // publish configuration file
        $this->publishes([
            $this->configFile() => $this->app['path.config'] . DIRECTORY_SEPARATOR . 'atos.php',
        ], 'config');

        $this->publishes([
            realpath(__DIR__ . '/../../views') => $this->app['path.base'] . DIRECTORY_SEPARATOR
                . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'vendor'
                . DIRECTORY_SEPARATOR . 'atos',
        ], 'views');
    }

    /**
     * Register service provider.
     */
    public function register()
    {
        // merge module config if it's not published or some entries are missing
        $this->mergeConfigFrom($this->configFile(), 'atos');
    }

    /**
     * Get module config file.
     *
     * @return string
     */
    protected function configFile()
    {
        return realpath(__DIR__ . '/../../config/atos.php');
    }
}
