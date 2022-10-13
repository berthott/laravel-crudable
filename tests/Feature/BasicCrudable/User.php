<?php

namespace berthott\Crudable\Tests\Feature\BasicCrudable;

use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Crudable, HasFactory;

    protected $appends = [
        'test',
    ];

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function getTestAttribute()
    {
        return 'test';
    }

    /**
     * Returns an array of additional middleware.
     */
    public static function filterFromSchema(): array
    {
        return ['hours'];
    }
}
