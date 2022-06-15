<?php

namespace berthott\Crudable\Services;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

const CACHE_KEY = 'CrudableService-Cache-Key';

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
        $this->crudables = Cache::sear(CACHE_KEY, function () {
            $crudables = [];
            $namespaces = config('crudable.namespace');
            foreach (is_array($namespaces) ? $namespaces : [$namespaces] as $namespace) {
                foreach (ClassFinder::getClassesInNamespace($namespace, config('crudable.namespace_mode')) as $class) {
                    foreach (class_uses_recursive($class) as $trait) {
                        if ('berthott\Crudable\Models\Traits\Crudable' == $trait) {
                            array_push($crudables, $class);
                        }
                    }
                }
            }
            return collect($crudables);
        });
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
