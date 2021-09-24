<?php

namespace berthott\Crudable\Tests\Feature\AttachOrCreateRelation;

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
        return UserFactory::new();
    }

    /**
     * Returns an array of relations that should
     * be attached or created automatically.
     * 'relationMethod' => [
     *      'class' => Relation::class,
     *      'creationMethod' => Closure,
     * ]
     */
    public static function creatables(): array
    {
        return [
            'tags' => [
                'class' => Tag::class,
                'creationMethod' => function ($value) {
                    return ['name' => $value];
                }
            ],
            'attributes' => [
                'class' => Attribute::class,
                'creationMethod' => function ($value) {
                    return ['name' => $value];
                }
            ],
        ];
    }

    /**
     * The tags that belong to the user.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * The tags that belong to the user.
     */
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class);
    }
}
