# Laravel-Crudable

A helper for CRUD routes in Laravel.

Easily add a complete CRUD route + controller by adding a trait to your model.

## Installation

```sh
$ composer require berthott/laravel-crudable
```

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

## Compatibility

Tested with Laravel 10.x.

## License

See [License File](license.md). Copyright © 2023 Jan Bladt.