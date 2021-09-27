<?php

namespace berthott\Crudable\Services;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

class CrudRelationsService
{
    /**
     * Attach an existing relation for the model.
     */
    public function attach(Model $model, array $data): Model
    {
        $model = $this->attachExisting($model, $data);

        return $this->attachOrCreate($model, $data);
    }

    /**
     * Attach an existing relation for the model.
     */
    public function attachExisting(Model $model, array $data): Model
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
     * Attach or create the relations for the model.
     */
    public function attachOrCreate(Model $model, array $data): Model
    {
        $creatables = $model->creatables();
        foreach ($this->getPossibleRelations($model, array_keys($creatables)) as $relation) {
            if (array_key_exists($relation->name, $data)) {
                $relation->invoke($model)->detach();
                $relationClass = $creatables[$relation->name]['class'];
                $creationMethod = $creatables[$relation->name]['creationMethod'];
                foreach ($data[$relation->name] as $dataEntry) {
                    $relationInstance = $relationClass::firstOrCreate($creationMethod($dataEntry));
                    $relation->invoke($model)->attach($relationInstance);
                }
                // delete unrelated
                $creatables[$relation->name]['class']::doesntHave($model->getTable())->delete();
                $model->load($relation->name);
            }
        }

        return $model;
    }

    /**
     * Attach or create the relations for the model.
     */
    public function deleteUnrelatedCreatables(string $class): void
    {
        $creatables = $class::creatables();
        $instance = new $class();
        foreach ($this->getPossibleRelations($class, array_keys($creatables)) as $relation) {
            $creatables[$relation->name]['class']::doesntHave($instance->getTable())->delete();
        }
    }

    /**
     * Reflect the given model and search for relations based on the relation name.
     */
    public function getPossibleRelations(Model|string $model, array $methods): array
    {
        $reflector = new ReflectionClass($model);
        $relations = [];
        foreach ($reflector->getMethods() as $reflectionMethod) {
            if (in_array($reflectionMethod->name, $methods, true)) {
                $relations[] = $reflectionMethod;
            }
        }

        return $relations;
    }
}
