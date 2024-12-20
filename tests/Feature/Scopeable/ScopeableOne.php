<?php

namespace berthott\Crudable\Tests\Feature\Scopeable;

use berthott\Crudable\Models\Traits\Crudable;
use berthott\Scopeable\Models\Traits\Scopeable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScopeableOne extends Model
{
    use Crudable, HasFactory, Scopeable;

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
        return ScopeableOneFactory::new();
    }
}
