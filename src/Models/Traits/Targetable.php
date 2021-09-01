<?php

namespace berthott\Crudable\Models\Traits;

use berthott\Crudable\Facades\Crudable;

trait Targetable
{
    /**
     * The target model.
     * @var string
     */
    private $target;

    /**
     * @param string $target
     *
     * @return void
     */
    public function initTarget(): void {
        $this->target = Crudable::getTarget();
    }
}