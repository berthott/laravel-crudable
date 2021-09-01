<?php

namespace berthott\Crudable\Models\Contracts;

interface Crudable
{
    /**
     * @return array
     */
    public static function rules(): array;
}