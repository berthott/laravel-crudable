<?php

namespace berthott\Crudable\Services;

use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;

class CrudQueryService
{
    /**
     * Attach an existing relation for the model.
     */
    public function getQuery(string $class): Collection
    {
        return QueryBuilder::for($class)
            ->allowedFilters($this->fromOptions($class, 'filter'))
            ->allowedSorts($this->fromOptions($class, 'sort'))
            ->allowedFields($this->fromOptions($class, 'fields'))
            ->allowedIncludes($this->fromOptions($class, 'include'))
            ->allowedAppends($this->fromOptions($class, 'append'))
            ->get();
    }

    /**
     * Get the user defined array from the options.
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
