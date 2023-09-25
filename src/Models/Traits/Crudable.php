<?php

namespace berthott\Crudable\Models\Traits;

use Illuminate\Database\Schema\Grammars\MySqlGrammar;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Trait to add the crudable functionality.
 */
trait Crudable
{
    /**
     * @var array(string) $tableColumns cache the table columns
     */
    private static array $tableColumns;

    /**
     * Initialize the crudable.
     * 
     * Autoset the fillable array.
     */
    protected function initializeCrudable(): void
    {
        $this->fillable(array_merge(array_diff(self::getTableColumns(), ['id']), $this->fillable));
    }

    /**
     * Returns an array of route options.
     * 
     * **optional**
     * 
     * Defaults to `[]`.
     * 
     * @link https://laravel.com/docs/10.x/controllers#api-resource-routes Route::apiResource
     * @see \berthott\SX\SxServiceProvider::$routes
     * @api
     */
    public static function routeOptions(): array
    {
        return [];
    }

    /**
     * Returns an array of query builder options.
     * 
     * **optional**
     * 
     * Possible options are: filter, sort, include, fields, append.
     * 
     * The array might look like this:
     * 
     * ```php
     * [
     *      'filter' => ['id', 'name'],
     *      'sort' => ['name'],
     * ]
     * ```
     * 
     * Defaults to `[]`.
     * 
     * @link https://spatie.be/docs/laravel-query-builder/v3/introduction
     * @api
     */
    public static function queryBuilderOptions(): array
    {
        return [];
    }

    /**
     * Register routes that should be evaluated before the CRUD routes.
     * 
     * **optional**
     * 
     * {@see \berthott\Crudable\Models\Traits\Crudable::middleware()} won't be applied.
     * 
     * @api
     */
    public static function routesBefore()
    {
        //
    }

    /**
     * Register routes that should be evaluated after the CRUD routes.
     * 
     * **optional**
     * 
     * {@see \berthott\Crudable\Models\Traits\Crudable::middleware()} won't be applied.
     * 
     * @api
     */
    public static function routesAfter()
    {
        //
    }

    /**
     * Returns an array of relations that should be attached automatically.
     * 
     * **optional**
     * 
     * The array should contain the relation method names that exist on the model.
     * 
     * Defaults to `[]`.
     * 
     * @api
     */
    public static function attachables(): array
    {
        return [];
    }

    /**
     * Returns an array of relations that should be attached or created automatically.
     * 
     * **optional**
     * 
     * The array may look like this:
     * ```php
     * 'relationMethod' => [
     *      'class' => Relation::class,
     *      'creationMethod' => Closure,
     * ]
     * ```
     * 
     * Defaults to `[]`.
     * 
     * @api
     */
    public static function creatables(): array
    {
        return [];
    }

    /**
     * Returns an array of custom relations that will be looped through.
     * 
     * **optional**
     * 
     * The array should be a associative with the relation method names as keys
     * and a Closure as value.
     * 
     * Defaults to `[]`.
     * 
     * @api
     */
    public static function customRelations(): array
    {
        return [];
    }

    /**
     * Returns an array of additional middlewares.
     * 
     * Middleware will be applied to all routes except {@see \berthott\Crudable\Models\Traits\Crudable::routesBefore()} 
     * and {@see \berthott\Crudable\Models\Traits\Crudable::routesAfter()} in addition to the
     * *crudable.middleware* configured middlewares.
     * 
     * Defaults to `[]`.
     * 
     * @api
     */
    public static function middleware(): array
    {
        return [];
    }

    /**
     * The validation rules that should be applied to store and update requests.
     * 
     * **optional**
     * 
     * Defaults to `[]`.
     * 
     * @api
     */
    public static function rules(mixed $id): array
    {
        return [];
    }

    /**
     * Returns an array of columns to be filtered from the schema.
     * 
     * **optional**
     * 
     * Defaults to `[]`.
     * 
     * @api
     */
    public static function filterFromSchema(): array
    {
        return [];
    }

    /**
     * Returns an array of relations to add to the show route.
     * 
     * **optional**
     * 
     * Defaults to `[]`.
     * 
     * @api
     */
    public static function showRelations(): array
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

    /**
     * Return the table columns.
     * 
     * If running in a unit test the columns will be returned by Laravels Schema facade.
     */
    private static function getTableColumns(): array
    {
        if (!isset(static::$tableColumns)) {
            static::$tableColumns = App::runningUnitTests()
                ? Schema::getColumnListing(self::entityTableName())
                : array_map(function ($column) {
                        return $column->column_name;
                    }, DB::getSchemaBuilder()->getConnection()->select(
                        (new MySqlGrammar())->compileColumnListing().' order by ordinal_position',
                        [DB::getSchemaBuilder()->getConnection()->getDatabaseName(), self::entityTableName()]
                    ));
        }
        return static::$tableColumns;
    }

    /**
     * Builds the schema for the appended columns.
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
