<?php

namespace berthott\Crudable\Tests\Feature\Middlewares;

use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use Crudable;
}
