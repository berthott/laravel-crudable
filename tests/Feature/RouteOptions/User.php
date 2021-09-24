<?php

namespace berthott\Crudable\Tests\Feature\RouteOptions;

use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Crudable;

    /**
     * Returns an array of route options.
     * See Route::apiResource documentation.
     */
    public static function routeOptions(): array
    {
        return [
            'only' => ['index']
        ];
    }
}
