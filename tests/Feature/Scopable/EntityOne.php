<?php

namespace berthott\Crudable\Tests\Feature\Scopable;

use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityOne extends Model
{
    use Crudable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'scopable_one_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'scopable_one_id' => 'integer',
    ];

    /**
     * @param  mixed  $id
     * @return array
     */
    public static function rules($id): array
    {
        return [
            'name' => 'required',
            'scopable_one_id' => 'required',
        ];
    }

    protected static function newFactory()
    {
        return EntityOneFactory::new();
    }

    public function scopable_one()
    {
        return $this->belongsTo(ScopableOne::class);
    }
}
