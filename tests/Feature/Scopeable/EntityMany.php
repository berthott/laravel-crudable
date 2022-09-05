<?php

namespace berthott\Crudable\Tests\Feature\Scopeable;

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
            'scopeable_manies.*' => 'nullable|numeric'
        ];
    }

    protected static function newFactory()
    {
        return EntityManyFactory::new();
    }

    /**
     * Returns an array of foreign keys that should
     * be attached automatically.
     */
    public static function attachables(): array
    {
        return [
            'scopeable_manies',
        ];
    }

    public function scopeable_manies()
    {
        return $this->belongsToMany(ScopeableMany::class);
    }
}
