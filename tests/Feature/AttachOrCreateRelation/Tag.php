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
     * Delete all unused tags.
     */
    static public function deleteUnused() 
    {
        self::doesntHave('users')
            ->doesntHave('projects')
            ->delete();
    }

    /**
     * The users that belong to the tag.
     */
    public function users()
    {
        return $this->morphedByMany(User::class, 'taggable');
    }

    /**
     * The projects that belong to the tag.
     */
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'taggable');
    }
}
