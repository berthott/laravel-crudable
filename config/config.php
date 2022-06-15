<?php

use HaydenPierce\ClassFinder\ClassFinder;

return [

    /*
    |--------------------------------------------------------------------------
    | Route Middleware Configuration
    |--------------------------------------------------------------------------
    |
    | Configurations for the route.
    |
    */

    'middleware' => ['api'],

    /*
    |--------------------------------------------------------------------------
    | Model Namespace Configuration
    |--------------------------------------------------------------------------
    |
    | Defines one or multiple model namespaces.
    |
    */

    'namespace' => 'App\Models',

    /*
    |--------------------------------------------------------------------------
    | Model Namespace Search Option
    |--------------------------------------------------------------------------
    |
    | Defines the search mode for the namespaces. ClassFinder::STANDARD_MODE
    | will only find the exact matching namespace, ClassFinder::RECURSIVE_MODE
    | will find all subnamespaces. Beware: ClassFinder::RECURSIVE_MODE might 
    | cause some testing issues.
    |
    */

    'namespace_mode' => ClassFinder::STANDARD_MODE,

    /*
    |--------------------------------------------------------------------------
    | API Prefix
    |--------------------------------------------------------------------------
    |
    | Defines the api prefix.
    |
    */

    'prefix' => 'api',
];
