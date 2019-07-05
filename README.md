This project is no longer being maintained. Please switch to foolz/sphinxql-query-builder.

Scorpio SphinxSearchBundle
==========================

SphinxSearchBundle adds configuration and service support to Scorpio SphinxSearch,
making it easier to use in a Laravel project.

Requirements
------------

 * Laravel 5.2+
 * Scorpio SphinxSearch
 * for composer installs, PHP Sphinx extension

Installation
------------

 1. The preferred method is to install via composer:

    composer require scorpio/sphinx-search-laravel "0.1.*"

 2. Enable the service provider in your config/app.php:

    // config/app.php
    \Scorpio\SphinxSearchLaravel\SphinxSearchServiceProvider::class

 3. ./artisan vendor:publish

 4. Set the configuration parameters in config/sphinx.php

 5. And map some indexes (see later)

Basic Usage
-----------

This bundle exposes the following configuration:

    host: localhost
    port: 9312
    max_query_time: 5000 # max query execution time

Optionally a specific SphinxClient class can be specified to handle the connections.
This can be used if the PHP extension is not available and the SphinxQL library
cannot be used.

    client_class: SomeClass\That\Implements\SphinxClientAPI::class

The following services are automatically registered:

 * scorpio_sphinx_search.server.settings (server settings)
 * scorpio_sphinx_search.search_manager  (main search manager instance)

Indexes can be defined in the config file and will be published as services:

```php
'indexes' => [
    'my_custom_sphinx_index' => [
        'class'     => \Scorpio\SphinxSearch\SearchIndex::class,
        'arguments' => [
            'index'        => 'my_custom_sphinx_index',
            'fields'       => [ 'available', 'fields', 'as_an_array' ],
            'attributes'   => [ 'attribute1', 'attribute2' ],
        ],
    ],
],
```

Note: the index name and fields are required and must match what is exposed in the
Sphinx configuration.

Additionally the result set and result record class can also be specified:

```php
'indexes' => [
    'my_custom_sphinx_index' => [
        'class'     => \Scorpio\SphinxSearch\SearchIndex::class,
        'arguments' => [
            'index'        => 'my_custom_sphinx_index',
            'fields'       => [ 'available', 'fields', 'as_an_array' ],
            'attributes'   => [ 'attribute1', 'attribute2' ],
            'result_set'   => MyResultSet::class,
            'result_class' => MyCustomResult::class,
        ],
    ],
],
```

Finally, for the really lazy!, the index can have a 'query' element added:

```php
'indexes' => [
    'my_custom_sphinx_index' => [
        'class'     => \Scorpio\SphinxSearch\SearchIndex::class,
        'query'     => true,
        'arguments' => [
            'index'        => 'my_custom_sphinx_index',
            'fields'       => [ 'available', 'fields', 'as_an_array' ],
            'attributes'   => [ 'attribute1', 'attribute2' ],
        ],
    ],
],
```

A custom query service will be automatically registered in the container. The prefix
can be customised in your config file with the default being "query.", so the
previous example would create the service: "query.my_custom_sphinx_index".

Note: the attribute "query" must be set to true, otherwise the index will be ignored.
This allows the services to be tagged and locatable for debugging but not auto-create
a query service when not needed.

In your controller you can then access the query instance:

```php
class MyController extends Controller
{

    function indexAction(Request $request)
    {
        // bind a search term somehow, apply filters etc. maybe check for keywords...
        $query = app('query.my_custom_sphinx_index')
            ->setQuery($request->query->get('keywords'));

        $results = app('scorpio_sphinx_search.search_manager')->query($query);

        // do something with the results.
    }
}
```

For Laravel, you are encouraged to create custom SearchIndex classes for each index,
and to then name then using the class name. Then they can be injected as dependencies.

License
-------

This library is licensed under the BSD license. See the complete license in the included
LICENSE file.

Issues or feature requests
---------------------------

Issues and feature requests should be made on the [Github repository page](https://github.com/scorpioframework/sphinx-search-laravel/issues).
