<?php

namespace berthott\Crudable\Tests\Feature\AttachOrCreateRelation;

use berthott\Crudable\Models\Contracts\Crudable as ContractsCrudable;
use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectFactory extends NameFactory
{
    protected $model = Project::class;
};

class Project extends Model implements ContractsCrudable
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
        return ProjectFactory::new();
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
        ];
    }

    /**
     * The tags that belong to the user.
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
