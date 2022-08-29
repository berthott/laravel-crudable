<?php

namespace berthott\Crudable\Tests\Feature\Scopable;

use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityMany extends Model
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
        return EntityManyFactory::new();
    }

    public function scopableManies()
    {
        return $this->belongsToMany(ScopableMany::class);
    }
}
