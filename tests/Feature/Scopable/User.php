<?php

namespace berthott\Crudable\Tests\Feature\Scopable;

use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Crudable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @param  mixed  $id
     * @return array
     */
    public static function rules($id): array
    {
        return [
            'name' => 'required',
        ];
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function scopable_one()
    {
        return $this->belongsTo(ScopableOne::class);
    }

    public function scopable_manies()
    {
        return $this->belongsToMany(ScopableMany::class);
    }
}
