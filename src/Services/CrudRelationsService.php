<?php

namespace berthott\Crudable\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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
            $key = $this->getDataKey($relation->name, $data);
            if ($key) {
                $relation->invoke($model)->detach();
                $relation->invoke($model)->attach($data[$key]);
                $model->load($relation->name);
                $this->sendUpdateEvent($model);
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
            $key = $this->getDataKey($relation->name, $data);
            if ($key) {
                $relation->invoke($model)->detach();
                $relationClass = $creatables[$relation->name]['class'];
                $creationMethod = $creatables[$relation->name]['creationMethod'];
                if (!is_array($data[$key])) {
                    $data[$key] = [$data[$key]];
                }
                foreach ($data[$key] as $dataEntry) {
                    $relationInstance = $relationClass::firstOrCreate($creationMethod($dataEntry));
                    $relation->invoke($model)->attach($relationInstance);
                }
                // delete unrelated
                $creatables[$relation->name]['class']::doesntHave($model->getTable())->delete();
                $model->load($relation->name);
                $this->sendUpdateEvent($model);
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
            $this->sendUpdateEvent($instance);
        }
    }

    /**
     * Reflect the given model and search for relations based on the relation name.
     */
    private function getPossibleRelations(Model|string $model, array $methods): array
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

    /**
     * Send an update event
     */
    private function sendUpdateEvent(Model $model)
    {
        event('eloquent.updated: '.get_class($model), $model);
    }

    /**
     * Get plural or singular data key
     */
    private function getDataKey(string $relationName, array $data): string | null
    {
        $singleName = Str::singular($relationName);
        if (array_key_exists($relationName, $data)) {
            return $relationName;
        } else if (array_key_exists($singleName, $data)) {
            return $singleName;
        } else {
            return null;
        }
    }
}
