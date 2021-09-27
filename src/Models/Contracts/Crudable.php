<?php

namespace berthott\Crudable\Models\Contracts;

interface Crudable
{
    public static function queryBuilderOptions(): array;
    public static function attachables(): array;
    public static function creatables(): array;
    public static function rules(mixed $id): array;
}
