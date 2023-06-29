<?php

namespace berthott\Crudable\Services;

use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Service for adding query parameters to the request.
 * 
 * @link https://spatie.be/docs/laravel-query-builder/v5/introduction spatie/laravel-query-builder
 */
class CrudQueryService
{
    /**
     * Setup QueryBuilder with the configured query parameters and get the collection.
     */
    public function getQuery(string $class): Collection
    {
        return QueryBuilder::for($class)
            ->allowedFilters($this->fromOptions($class, 'filter'))
            ->allowedSorts($this->fromOptions($class, 'sort'))
            ->allowedFields($this->fromOptions($class, 'fields'))
            ->allowedIncludes($this->fromOptions($class, 'include'))
            ->get();
    }

    /**
     * Get the user defined array from the options.
     * 
     * @see \berthott\Crudable\Tests\Feature\QueryBuilder\User::queryBuilderOptions()
     * @link https://spatie.be/docs/laravel-query-builder/v5/introduction spatie/laravel-query-builder
     */
    protected function fromOptions(string $class, string $attribute): array
    {
        $options = $class::queryBuilderOptions();
        if (array_key_exists($attribute, $options)) {
            return $options[$attribute];
        }

        return [];
    }
}
