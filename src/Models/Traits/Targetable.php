<?php

namespace berthott\Crudable\Models\Traits;

use berthott\Crudable\Facades\Crudable;

trait Targetable
{
    /**
     * The target model.
     */
    private string $target;

    public function initTarget(): void
    {
        $this->target = Crudable::getTarget();
    }
}
