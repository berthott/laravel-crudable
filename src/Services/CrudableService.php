<?php

namespace berthott\Crudable\Services;

use berthott\Targetable\Services\TargetableService;
use berthott\Crudable\Models\Traits\Crudable;

/**
 * TargetableService implementation for an sxable class.
 * 
 * @link https://docs.syspons-dev.com/laravel-targetable
 */
class CrudableService extends TargetableService
{
    public function __construct()
    {
        parent::__construct(Crudable::class, 'crudable');
    }
}
