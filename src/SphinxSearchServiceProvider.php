<?php

/*
 * This file is part of the Scorpio SphinxSearch Laravel Bundle.
 *
 * (c) Dave Redfern <dave@scorpioframework.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Scorpio\SphinxSearchLaravel;

use Illuminate\Support\ServiceProvider;
use Scorpio\SphinxSearch\SearchManager;
use Scorpio\SphinxSearch\SearchQuery;
use Scorpio\SphinxSearch\ServerSettings;

/**
 * Class TenancyServiceProvider
 *
 * @package    Somnambulist\Tenancy\Providers
 * @subpackage Somnambulist\Tenancy\Providers\TenancyServiceProvider
 * @author     Dave Redfern
 */
class SphinxSearchServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([ $this->getConfigPath() => config_path('sphinx.php'), ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();

        $this->registerServerSettings();
        $this->registerSearchQueryProvider();
        $this->registerIndexDefinitions();
    }



    /**
     * Merge config
     */
    protected function mergeConfig()
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'sphinx');
    }

    /**
     * Register the Sphinx server settings instance
     *
     * @return void
     */
    protected function registerServerSettings()
    {
        $this->app->singleton('scorpio_sphinx_search.server.settings', function ($app) {
            $config = $app->make('config');

            if ( !extension_loaded('sphinx') && !$config->get('sphinx.client_class') ) {
                throw new \RuntimeException(
                    sprintf('SphinxClientAPI is not available and a client_class was not defined')
                );
            }

            return new ServerSettings(
                $config->get('sphinx.host'),
                $config->get('sphinx.port'),
                $config->get('sphinx.max_query_time'),
                $config->get('sphinx.client_class')
            );
        });
    }

    /**
     * Registers the search manager that holds the indexes
     *
     * @return void
     */
    protected function registerSearchQueryProvider()
    {
        $this->app->singleton('scorpio_sphinx_search.search_query_provider', function ($app) {
            return new SearchManager($app['scorpio_sphinx_search.server.settings']);
        });
    }

    /**
     * Register the indexes
     *
     * @return void
     */
    protected function registerIndexDefinitions()
    {
        /** @var SearchManager $provider */
        $provider = $this->app->make('scorpio_sphinx_search.search_query_provider');
        $config   = $this->app->make('config');
        $prefix   = $config->get('sphinx.query_search_prefix');

        foreach ($config->get('sphinx.indexes', []) as $service => $details) {
            $this->app->singleton($service, function ($app) use ($details) {
                $class  = $details['class'];
                $args   = $details['arguments'];
                $set    = isset($args['result_set']) ? $args['result_set'] : null;
                $result = isset($args['result_class']) ? $args['result_class'] : null;

                return new $class($args['index'], $args['fields'], $args['attributes'], $set, $result);
            });

            if (isset($details['query']) && $details['query']) {
                $this->app->instance($prefix . $service, new SearchQuery($this->app[$service]));
                $provider->addQuery($this->app[$prefix . $service]);
            }
        }
    }

    /**
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__ . '/../config/sphinx.php';
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            'scorpio_sphinx_search.server.settings',
            'scorpio_sphinx_search.search_query_provider',
        ];
    }
}