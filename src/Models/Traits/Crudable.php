<?php

namespace berthott\Crudable\Models\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait Crudable
{
    /**
     * Returns an array of query builder options.
     * See https://spatie.be/docs/laravel-query-builder/v3/introduction
     * Options are: filter, sort, include, fields, append.
     */
    public static function queryBuilderOptions(): array
    {
        return [];
    }

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
     * ].
     */
    public static function creatables(): array
    {
        return [];
    }

    public static function rules(mixed $id): array
    {
        return [];
    }

    /**
     * The single name of the model.
     */
    public static function singleName(): string
    {
        return Str::snake(class_basename(get_called_class()));
    }

    /**
     * The entity table name of the model.
     */
    public static function entityTableName(): string
    {
        return Str::snake(Str::pluralStudly(class_basename(get_called_class())));
    }

    /**
     * Returns the schema of the current entity.
     */
    public static function schema(): array
    {
        if (!Schema::hasTable(self::entityTableName())) {
            return [];
        }
        return array_map(function ($column) {
            $type = DB::getSchemaBuilder()->getColumnType(self::entityTableName(), $column);
            return [
                'column' => $column,
                'type' => $type,
            ];
        }, Schema::getColumnListing(self::entityTableName()));
    }
}
