<?php

namespace berthott\Crudable\Tests\Feature\Scopeable;

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

    public function scopeable_one()
    {
        return $this->belongsTo(ScopeableOne::class);
    }

    public function scopeable_manies()
    {
        return $this->belongsToMany(ScopeableMany::class);
    }
}
