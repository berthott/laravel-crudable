<?php

namespace berthott\Crudable\Models\Contracts;

interface Crudable
{
    /**
     * @param  mixed  $id
     * @return array
     */
    public static function rules($id): array;
}