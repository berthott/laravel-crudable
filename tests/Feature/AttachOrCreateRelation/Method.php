<?php

namespace berthott\Crudable\Tests\Feature\AttachOrCreateRelation;

use berthott\Crudable\Models\Contracts\Crudable as ContractsCrudable;
use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MethodFactory extends NameFactory
{
    protected $model = Method::class;
};

class Method extends Model implements ContractsCrudable
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
        return MethodFactory::new();
    }

    /**
     * The users that belong to the tag.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
