<?php

namespace berthott\Crudable\Services;

use Illuminate\Database\Eloquent\Model;

/**
 * Service to add attributes and relations when needed
 */
class CrudAttributesService
{
    /**
     * Attach relations to the model or show hidden attributes.
     */
    public function get(Model $model): Model
    {
        $relations = [];
        $attributes = [];
        foreach($model->showRelations() as $show) {
            if (method_exists($model, $show)) {
                $relations[] = $show;
            } else if ($model->$show) {
                $attributes[] = $show;
            }
        }
        return $model->load($relations)->makeVisible($attributes);
    }
}
