<?php

namespace berthott\Crudable\Facades;

use Illuminate\Support\Facades\Facade;

class CrudQuery extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'CrudQuery';
    }
}
