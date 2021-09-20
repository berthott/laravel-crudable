<?php

namespace berthott\Crudable\Models\Contracts;

interface Crudable
{
    public static function rules(mixed $id): array;
}
