<?php

namespace berthott\Crudable\Services;

use berthott\Crudable\Exceptions\ForbiddenException;
use Closure;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;

const SCOPABLE_CACHE_KEY = 'ScopableService-Cache-Key';

class ScopableService
{
    /**
     * Collection with all classes.
     */
    private Collection $classes;

    /**
     * The Constructor.
     */
    public function __construct()
    {
        $this->initClasses();
    }

    /**
     * Get the classes collection.
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    /**
     * Initialize the classes collection.
     */
    private function initClasses(): void
    {
        $this->classes = Cache::sear(SCOPABLE_CACHE_KEY, function () {
            $classes = [];
            $namespaces = config('crudable.namespace');
            foreach (is_array($namespaces) ? $namespaces : [$namespaces] as $namespace) {
                foreach (ClassFinder::getClassesInNamespace($namespace, ClassFinder::RECURSIVE_MODE) as $class) {
                    foreach (class_uses_recursive($class) as $trait) {
                        if ('berthott\Crudable\Models\Traits\Scopable' == $trait) {
                            array_push($classes, $class);
                        }
                    }
                }
            }
            return collect($classes);
        });
    }

    /**
     * Is the class scopable?
     */
    public function isScopable(string $model): bool
    {
        return $this->classes->first(function ($class) use ($model) {
            return $class === $model;
        }) ? true : false;
    }

    /**
     * Get all scopable relations
     */
    public function getScopableRelations(Model|string $model): array
    {
        $reflector = new ReflectionClass($model);
        $relations = [];
        foreach ($reflector->getMethods() as $reflectionMethod) {
            foreach ($this->classes->toArray() as $scopable) {
                if (Str::snake(Str::plural(Arr::last(explode('\\', $scopable)))) === $reflectionMethod->name
                 || Str::snake(Arr::last(explode('\\', $scopable))) === $reflectionMethod->name) {
                    $relations[] = $reflectionMethod;
                }
            }
        }

        return $relations;
    }

    /**
     * Has the model the allowed scope
     */
    public function isAllowedInScopes(Model $model): bool
    {
        $allowed = true;
        foreach ($this->getScopableRelations($model) as $relationMethod) {
            $modelInstances = $relationMethod->invoke($model)->get();
            $method = $relationMethod->name;
            $userInstances = Auth::user()->$method()->get();
            if ($modelInstances instanceof Collection) {
                if ($modelInstances->intersect($userInstances)->isEmpty()) {
                    $allowed = false;
                }
            } else {
                if ($modelInstances->id != $userInstances->id) {
                    $allowed = false;
                }
            }
        }
        return $allowed;
    }

    /**
     * Filter models with allowed scope
     */
    public function filterScopes(Collection $models): Collection
    {
        return $models->filter(function ($model) {
            return $this->isAllowedInScopes($model);
        });
    }

    /**
     * Filter models with allowed scope
     * 
     * @throws ForbiddenException
     */
    public function checkScopes(Model $model, Closure $callback = NULL): Model
    {
        if (!$this->isAllowedInScopes($model)) {
            if ($callback) {
                $callback($model);
            }
            throw new ForbiddenException;
        }
        return $model;
    }
}
