<?php

namespace berthott\Crudable\Facades;

use Illuminate\Support\Facades\Facade;

class Crudable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Crudable';
    }
}
