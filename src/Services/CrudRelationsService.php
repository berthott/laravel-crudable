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
        foreach ($this->getPossibleRelations($model, $model->attachables()) as $relation) {
            if (array_key_exists($relation->name, $data)) {
                $relation->invoke($model)->detach();
                $relation->invoke($model)->attach($data[$relation->name]);
                $model->load($relation->name);
            }
        }
        return $model;
    }

    /**
     * Reflect the given model and search for relations based on the relation name
     */
    public function getPossibleRelations(Model $model, array $methods): array
    {
        $reflector = new ReflectionClass($model);
        $relations = [];
        foreach ($reflector->getMethods() as $reflectionMethod) {
            if (in_array($reflectionMethod->name, $methods)) {
                $relations[] = $reflectionMethod;
            }
        }
        return $relations;
    }
}
