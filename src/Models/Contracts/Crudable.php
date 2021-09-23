<?php

namespace berthott\Crudable\Models\Contracts;

interface Crudable
{
    public static function attachables(): array;
    public static function rules(mixed $id): array;
}
