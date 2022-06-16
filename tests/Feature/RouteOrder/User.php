<?php

namespace berthott\Crudable\Tests\Feature\RouteOrder;

use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class User extends Model
{
    use Crudable;

    /**
     * Register routes that should be evaluated 
     * before the CRUD routes.
     */
    public static function routesBefore()
    {
        Route::get('/users/before', function() {})->name('users.before');
    }

    /**
     * Register routes that should be evaluated 
     * after the CRUD routes.
     */
    public static function routesAfter()
    {
        Route::get('/users/after', function() {})->name('users.after');
    }
}
