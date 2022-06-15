<?php

namespace berthott\Crudable\Tests\Feature\NamespaceConfig\User;

use berthott\Crudable\Models\Traits\Crudable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Crudable;
}
