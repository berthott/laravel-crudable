<?php

namespace berthott\Crudable\Http\Requests;

use berthott\Crudable\Models\Contracts\Targetable;
use berthott\Crudable\Models\Traits\Targetable as TraitsTargetable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateRequest extends FormRequest implements Targetable 
{
  use TraitsTargetable;

  /** 
   * @var Model $instance 
   */
  private $instance;

  /**
 * Get the validation rules that apply to the request.
 *
 * @return array
 */
  public function rules()
  {
    return array_merge(array_fill_keys($this->getInstance()->getFillable(), 'nullable'), $this->target::rules($this->getPrimaryId()));
  }

  /**
   * @return Model
   */
  protected function getInstance(): Model
  {
    if (!$this->instance) {
      $this->instance = new $this->target;
    }
    return $this->instance;
  }

  /**
   * @return string
   */
  protected function getSingularName(): string
  {
      return Str::singular($this->getInstance()->getTable($this->target));
  }

  /**
   * @return bool
   */
  protected function isUpdate(): bool
  {
      return !empty($this->route($this->getSingularName()));
  }

  /**
   * @return mixed
   */
  protected function getPrimaryId()
  {
    return $this->isUpdate() ? $this->route($this->getSingularName()) : null;
  }

}