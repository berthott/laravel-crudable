<?php

namespace berthott\Crudable\Tests\Feature\AttachOrCreateRelation;

use berthott\Crudable\Models\Contracts\Crudable as ContractsCrudable;
use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagFactory extends NameFactory
{
    protected $model = Tag::class;
};

class Tag extends Model implements ContractsCrudable
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
        return TagFactory::new();
    }

    /**
     * The users that belong to the tag.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
