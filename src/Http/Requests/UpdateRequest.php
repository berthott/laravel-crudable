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

    private Model $instance;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return array_merge(
            array_fill_keys($this->getInstance()->getFillable(), 'nullable'),  // default fillable rules
            $this->buildAttachableRules(),  // default attachables rules
            $this->buildCreatableRules(),  // default creatables rules
            $this->target::rules($this->getPrimaryId()), // target rules
        );
    }

    protected function buildAttachableRules(): array
    {
        $rules = [];
        foreach ($this->target::attachables() as $attachable) {
            $rules[$attachable] = 'array';
            $rules[$attachable.'.*'] = 'nullable|integer|exists:'.$attachable.',id';
        }

        return $rules;
    }

    protected function buildCreatableRules(): array
    {
        $rules = [];
        foreach (array_keys($this->target::creatables()) as $creatable) {
            $rules[$creatable] = 'array';
        }

        return $rules;
    }

    protected function getInstance(): Model
    {
        if (!isset($this->instance)) {
            $this->instance = new $this->target();
        }

        return $this->instance;
    }

    protected function getSingularName(): string
    {
        return Str::singular($this->getInstance()->getTable($this->target));
    }

    protected function isUpdate(): bool
    {
        return !empty($this->route($this->getSingularName()));
    }

    protected function getPrimaryId(): mixed
    {
        return $this->isUpdate() ? $this->route($this->getSingularName()) : null;
    }
}
