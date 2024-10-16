# Laravel-Crudable

A helper for CRUD routes in Laravel.

Easily add a complete CRUD route + controller by adding a trait to your model.

## Installation

```sh
$ composer require berthott/laravel-crudable
```

## Concept

The package implements a generic approach onto CRUD routes moving the setup of those routes from the several different places (routes / controller) into the model itself. This does'nt prevent you from adding your own custom routes and controllers. There are helper methods if you need the additionally created route in a specific order.

### Frontend connection

The index route does not implement any pagination. This is to be considered when implementing the connection to this route as this means a potentially huge payload:
* Try to avoid eagerly loaded relations when they contain a lot of data (do not use them in `$with` array)
  * To add the information on the relations with minimal data size you can add an [attribute](https://laravel.com/docs/10.x/eloquent-mutators#accessors-and-mutators) holding the relations ids.
    TODO: This could be done by the package automatically
* If you wan't to eagerly load relations only in the show route you can use `showRelations()`

TODO: An optional pagination could help here in the future.

## Usage

* Create your table and corresponding model, eg. with `php artisan make:model YourModel -m`
* Add the `Crudable` trait to your newly generated model.
* The package will register these standard API CRUD routes (see [API Resource Routes](https://laravel.com/docs/10.x/controllers#api-resource-routes)).
  * Index, *get*     `yourmodels/` => get all entities
  * Show, *get*     `yourmodels/{yourmodel}` => get a single entity
  * Create, *post*    `yourmodels/` => create a new entity
  * Update, *put*    `yourmodels/{yourmodel}` => update an entity
  * Destroy, *delete*  `yourmodels/{yourmodel}` => delete an entity
* Additionally it registers
  * Destroy many, *delete*  `yourmodels/destroy_many` => delete many entities by their given ids
  * Schema, *get* `yourmodels/schema` => get the database schema
* Add relations implementing one of the following methods
  * `attachables()` to attach existing related models to the model
  * `creatables()` to create new related models and attach them to the model
  * `customRelations()` to implement your very own behavior for adding relations to the model
* For more information on how to setup certain features see `\berthott\Crudable\Models\Traits\Crudable`.

## Options

To change the default options use
```php
$ php artisan vendor:publish --provider="berthott\Crudable\CrudableServiceProvider" --tag="config"
```
* Inherited from [laravel-targetable](https://docs.syspons-dev.com/laravel-targetable)
* `namespace`: String or array with one ore multiple namespaces that should be monitored for the configured trait. Defaults to `App\Models`.
* `namespace_mode`: Defines the search mode for the namespaces. `ClassFinder::STANDARD_MODE` will only find the exact matching namespace, `ClassFinder::RECURSIVE_MODE`will find all subnamespaces. Defaults to `ClassFinder::STANDARD_MODE`.
* `prefix`: Defines the route prefix. Defaults to `api`.
* General Package Configuration
  * `middleware`: An array of all middlewares to be applied to all of the generated routes. Defaults to `['api']`.

## Architecture

* The package relies on [laravel-targetable](https://docs.syspons-dev.com/laravel-targetable) to connect specific functionality to Laravel model entities via a trait. (`Crudable`).


## Compatibility

Tested with Laravel 10.x.

## License

See [License File](license.md). Copyright © 2023 Jan Bladt.