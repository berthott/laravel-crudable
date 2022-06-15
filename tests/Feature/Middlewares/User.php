<?php

namespace berthott\Crudable\Tests\Feature\Middlewares;

use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Crudable;

    /**
     * Returns an array of additional middleware.
     */
    public static function middleware(): array
    {
        return ['test_middleware'];
    }
}
