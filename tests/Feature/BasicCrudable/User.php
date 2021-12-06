<?php

namespace berthott\Crudable\Tests\Feature\BasicCrudable;

use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected $appends = [
        'test',
    ];

    /**
     * @param  mixed  $id
     * @return array
     */
    public static function rules($id): array
    {
        return [
            'firstname' => 'required',
        ];
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function getTestAttribute()
    {
        return 'test';
    }
}
