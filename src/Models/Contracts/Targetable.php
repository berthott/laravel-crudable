<?php

namespace berthott\Crudable\Models\Contracts;

interface Targetable
{
    /**
     * @return void
     */
    public function initTarget(): void;
}