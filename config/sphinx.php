<?php

return [

    /*
     * The host for sphinx server
     */
    'host' => 'localhost',

    /*
     * The port
     */
    'port' => 9312,

    /*
     * Maximum execution time in MILLIseconds that a query should run.
     * 0 is infinite, minimum should be around 5000.
     */
    'max_query_time' => 5000,

    /*
     * Optional: a class that implements the SphinxClientAPI if not using the sphinx extension.
     */
    'client_class' => null,

    /*
     * The prefix for all auto-created query
     */
    'query_service_prefix' => 'query.',

    /*
     * Add your configured indexes here, they will be loaded and made available as services
     * within the container. The format is similar to the Symfony container yaml.
     *
     * Each index has:
     *  * a unique name that will be used as the service name,
     *  * a class that implements the index,
     *  * the arguments that define the index:
     *    * index - the actual index name that is defined in the sphinx config
     *    * fields - the fields that are available in the index
     *    * attributes - the attributes that can be filtered on
     *    * result_set - a class name for a custom result set (optional)
     *    * result_class - a class name for a custom result record (optional)
     *  * query - if true, a query instance will be created avoiding the need to set that bit up.
     *
     * Query instances are prefixed with query. by default (change above). So "service_name_for_index"
     * would create: "query.service_name_for_index" as the service in the container.
     */
    'indexes' => [

        'service_name_for_index' => [
            'class'     => \Scorpio\SphinxSearch\SearchIndex::class,
            //'query'     => true,
            'arguments' => [
                'index'        => 'index_name',
                'fields'       => [],
                'attributes'   => [],
                //'result_set'   => null,
                //'result_class' => null,
            ],
        ]
    ],

];