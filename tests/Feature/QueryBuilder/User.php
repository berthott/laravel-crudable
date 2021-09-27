<?php

namespace berthott\Crudable\Tests\Feature\QueryBuilder;

use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
}

class User extends Model
{
    use Crudable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
    ];

    /**
     * Returns an array of query builder options.
     * See https://spatie.be/docs/laravel-query-builder/v3/introduction
     * Options are: filter, sort, include, fields, append
     */
    public static function queryBuilderOptions(): array
    {
        return [
            'filter' => ['firstname'],
            'sort' => ['lastname'],
            'fields' => ['firstname'],
            'include' => ['roles'],
            'append' => ['fullname']
        ];
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function getFullnameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
