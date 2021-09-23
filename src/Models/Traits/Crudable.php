<?php

namespace berthott\Crudable\Models\Traits;

trait Crudable
{
    /**
     * Returns an array of foreign keys that should
     * be attached automatically.
     */
    public static function attachables(): array
    {
        return [];
    }

    public static function rules(/* mixed $id */): array
    {
        return [];
    }
}
