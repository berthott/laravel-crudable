<?php

namespace berthott\Crudable\Services;

use Illuminate\Support\Collection;
use HaydenPierce\ClassFinder\ClassFinder;
use \Illuminate\Support\Str;

class CrudableService
{
  /**
   * Collection with all crudable classes.
   * @var Collection
   */
  private $crudables;

  /**
   * The Constructor
   * 
   * @return void
   */
  public function __construct() {
    $this->initCrudableClasses();
  }

  /**
   * Get the crudable classes collection.
   * 
   * @return Collection
   */
  public function getCrudableClasses(): Collection {
    return $this->crudables;
  }

  /**
   * Initialize the crudable classes collection.
   * 
   * @return void
   */
  private function initCrudableClasses(): void {
    $crudables = [];
    foreach(ClassFinder::getClassesInNamespace(config('crudable.namespace')) as $class) {
      foreach(class_uses($class) as $trait) {
        if ($trait == 'berthott\Crudable\Models\Traits\Crudable') {
          array_push($crudables, $class);
        }
      }
    }
    $this->crudables = collect($crudables);
  }

  /**
   * Get the target model.
   * 
   * @return string
   */
  public function getTarget(): string {
    return request()->segments() 
      ? config('crudable.namespace').'\\'.Str::studly(Str::singular(request()->segment(count(explode('/', config('crudable.prefix'))) + 1)))
      : '';
  }
}
