<?php

namespace berthott\Crudable\Services;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class CrudableService
{
    /**
     * Collection with all crudable classes.
     */
    private Collection $crudables;

    /**
     * The Constructor.
     */
    public function __construct()
    {
        $this->initCrudableClasses();
    }

    /**
     * Get the crudable classes collection.
     */
    public function getCrudableClasses(): Collection
    {
        return $this->crudables;
    }

    /**
     * Initialize the crudable classes collection.
     */
    private function initCrudableClasses(): void
    {
        $crudables = [];
        $namespaces = config('crudable.namespace');
        foreach (is_array($namespaces) ? $namespaces : [$namespaces] as $namespace) {
            foreach (ClassFinder::getClassesInNamespace($namespace) as $class) {
                foreach (class_uses_recursive($class) as $trait) {
                    if ('berthott\Crudable\Models\Traits\Crudable' == $trait) {
                        array_push($crudables, $class);
                    }
                }
            }
        }
        $this->crudables = collect($crudables);
    }

    /**
     * Get the target model.
     */
    public function getTarget(): string
    {
        if (!request()->segments() || $this->crudables->isEmpty()) {
            return '';
        }
        $model = Str::studly(Str::singular(request()->segment(count(explode('/', config('permissions.prefix'))) + 1)));

        return $this->crudables->first(function ($class) use ($model) {
            return Arr::last(explode('\\', $class)) === $model;
        }) ?: '';
    }
}
