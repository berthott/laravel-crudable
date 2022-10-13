<?php

namespace berthott\Crudable\Models\Traits;

use Illuminate\Database\Schema\Grammars\MySqlGrammar;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait Crudable
{
    /**
     * Initialize the crudable.
     */
    protected function initializeCrudable(): void
    {
        $this->fillable(array_merge(array_diff(self::getTableColumns(), ['id']), $this->fillable));
    }

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
     * Register routes that should be evaluated 
     * before the CRUD routes.
     */
    public static function routesBefore()
    {
        //
    }

    /**
     * Register routes that should be evaluated 
     * after the CRUD routes.
     */
    public static function routesAfter()
    {
        //
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

    /**
     * Returns an array of custom relations that 
     * will be looped through.
     * 'relation' => Closure
     */
    public static function customRelations(): array
    {
        return [];
    }

    /**
     * Returns an array of additional middleware.
     */
    public static function middleware(): array
    {
        return [];
    }

    public static function rules(mixed $id): array
    {
        return [];
    }

    /**
     * Returns an array of additional middleware.
     */
    public static function filterFromSchema(): array
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
        return array_values(array_filter(
            array_merge(self::buildDbSchema(), self::buildAppendsSchema()), 
            fn ($entry) => !in_array($entry['column'], self::filterFromSchema()),
        ));
    }

    /**
     * Builds the schema of the current entity.
     */
    private static function buildDbSchema(): array
    {
        $table = self::entityTableName();
        $indexes = array_keys(DB::getSchemaBuilder()->getConnection()->getDoctrineSchemaManager()->listTableIndexes($table));
        return array_map(function ($column) use ($indexes, $table) {
            $doctrineColumn = DB::getSchemaBuilder()->getConnection()->getDoctrineColumn($table, $column);
            return [
                'column' => $column,
                'type' => $doctrineColumn->getType()->getName(),
                'nullable' => !$doctrineColumn->getNotNull(),
                'auto_increment' => $doctrineColumn->getAutoIncrement(),
                'length' => $doctrineColumn->getLength(),
                'default' => $doctrineColumn->getDefault(),
                'unique' => in_array("{$table}_{$column}_unique", $indexes),
                'foreign' => in_array("{$table}_{$column}_foreign", $indexes),
            ];
        }, self::getTableColumns());
    }

    private static function getTableColumns()
    {
        return App::runningUnitTests() 
        ?   Schema::getColumnListing(self::entityTableName())
        :   array_map(function($column) {
                return $column->column_name;
            }, DB::getSchemaBuilder()->getConnection()->select(
                (new MySqlGrammar)->compileColumnListing().' order by ordinal_position',
                [DB::getSchemaBuilder()->getConnection()->getDatabaseName(), self::entityTableName()]
            ));
    }

    /**
     * Builds the schema of the current entity.
     */
    private static function buildAppendsSchema(): array
    {

        return array_map(function ($appends) {
            return [
                'column' => $appends,
                'type' => 'appends',
            ];
        }, self::newModelInstance()->appends);
    }
}
