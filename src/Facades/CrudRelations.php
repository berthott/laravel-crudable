<?php

namespace berthott\Crudable\Facades;

use Illuminate\Support\Facades\Facade;

class CrudRelations extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'CrudRelations';
    }
}
