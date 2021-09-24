<?php

namespace berthott\Crudable\Models\Traits;

trait Crudable
{
    /**
     * Returns an array of route options.
     * See Route::apiResource documentation.
     */
    public static function routeOptions(): array
    {
        return [];
    }

    /**
     * Returns an array of relations that should
     * be attached automatically.
     */
    public static function attachables(): array
    {
        return [];
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
        return [];
    }

    public static function rules(mixed $id): array
    {
        return [];
    }
}
