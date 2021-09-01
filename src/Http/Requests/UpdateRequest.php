<?php

namespace berthott\Crudable\Http\Requests;

use berthott\Crudable\Models\Contracts\Targetable;
use berthott\Crudable\Models\Traits\Targetable as TraitsTargetable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest implements Targetable 
{
  use TraitsTargetable;

  /**
 * Get the validation rules that apply to the request.
 *
 * @return array
 */
  public function rules()
  {
    return array_merge(array_fill_keys($this->getInstance()->getFillable(), ''), $this->target::rules());
  }

  /**
   * @return Model
   */
  private function getInstance(): Model
  {
      return new $this->target;
  }

}