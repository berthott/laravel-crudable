<?php

namespace berthott\Crudable\Tests\Feature\ShowRelation;

use berthott\Crudable\Models\Contracts\Crudable as ContractsCrudable;
use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleFactory extends NameFactory
{
    protected $model = Role::class;
};
class Role extends Model implements ContractsCrudable
{
    use Crudable;
    use HasFactory;

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
        return RoleFactory::new();
    }

     /**
     * Returns an array of foreign keys that should
     * be attached automatically.
     */
    public static function attachables(): array
    {
        return [
            'tags'
        ];
    }

    /**
     * Returns an array of relations to add to the show route.
     */
    public static function showRelations(): array
    {
        return [];
    }

    /**
     * The tags that belong to the role.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
