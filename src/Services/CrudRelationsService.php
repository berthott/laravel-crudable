<?php

namespace berthott\Crudable\Services;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

class CrudRelationsService
{
    /**
     * Attach the relations for the model
     */
    public function attach(Model $model, array $data): Model
    {
        foreach ($this->getPossibleRelations($model, ['HasMany', 'BelongsToMany', 'MorphToMany']) as $relation) {
            if (array_key_exists($relation->name, $data)) {
                $relation->invoke($model)->detach();
                $relation->invoke($model)->attach($data[$relation->name]);
                $model->load($relation->name);
            }
        }
        return $model;
    }

    /**
     * Reflect the given model and search for relations based on the return type.
     * Works only if return type is defined!
     */
    public function getPossibleRelations(Model $model, array $methods = null): array
    {
        $reflector = new ReflectionClass($model);
        $relations = [];
        foreach ($reflector->getMethods() as $reflectionMethod) {
            $returnType = $reflectionMethod->getReturnType();
            if ($returnType) {
                if (in_array(class_basename($returnType->getName()), $methods ?: ['HasOne', 'HasMany', 'BelongsTo', 'BelongsToMany', 'MorphToMany', 'MorphTo'])) {
                    $relations[] = $reflectionMethod;
                }
            }
        }
        return $relations;
    }
}
