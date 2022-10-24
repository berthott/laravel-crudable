<?php

namespace berthott\Crudable\Tests\Feature\ShowRelation;

use berthott\Crudable\Models\Contracts\Crudable as ContractsCrudable;
use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFactory extends NameFactory
{
    protected $model = User::class;
};
class User extends Model implements ContractsCrudable
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
        return UserFactory::new();
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
        return ['tags'];
    }

    /**
     * The tags that belong to the user.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
