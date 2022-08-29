<?php

namespace berthott\Crudable\Facades;

use Illuminate\Support\Facades\Facade;

class Scopable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Scopable';
    }
}
