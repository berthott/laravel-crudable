<?php

namespace berthott\Crudable\Tests\Feature\AttachOrCreateRelation;

use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeFactory extends NameFactory
{
    protected $model = Attribute::class;
};

class Attribute extends Model
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

    protected static function newFactory()
    {
        return AttributeFactory::new();
    }

    /**
     * The users that belong to the tag.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
