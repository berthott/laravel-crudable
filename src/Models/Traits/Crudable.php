<?php

namespace berthott\Crudable\Models\Traits;


trait Crudable {
  
    /**
     * @param  mixed  $id
     * @return array
     */
    public static function rules($id): array {
      return [];
    }
}
