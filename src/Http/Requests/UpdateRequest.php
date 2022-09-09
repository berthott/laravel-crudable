<?php

namespace berthott\Crudable\Http\Requests;

use berthott\Crudable\Exceptions\ValidationException;
use berthott\Crudable\Models\Contracts\Targetable;
use berthott\Crudable\Models\Traits\Targetable as TraitsTargetable;
use Illuminate\Contracts\Validation\Validator;
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
            $this->buildCustomRelationRules(),  // default custom relation rules
            $this->buildDefaultRules($this->getPrimaryId()), // default rules from schema
            $this->target::rules($this->getPrimaryId()), // target rules
        );
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($this->validator->errors()->messages()));
    }

    protected function buildAttachableRules(): array
    {
        $rules = [];
        foreach ($this->target::attachables() as $attachable) {
            $rules[Str::singular($attachable)] = 'nullable|integer|exists:'.$attachable.',id';
            $rules[$attachable] = 'array';
            $rules[$attachable.'.*'] = 'nullable|integer|exists:'.$attachable.',id';
        }

        return $rules;
    }

    protected function buildCreatableRules(): array
    {
        $rules = [];
        foreach (array_keys($this->target::creatables()) as $creatable) {
            $rules[Str::singular($creatable)] = 'nullable';
            $rules[$creatable] = 'array';
        }

        return $rules;
    }

    protected function buildCustomRelationRules(): array
    {
        $rules = [];
        foreach (array_keys($this->target::customRelations()) as $customRelations) {
            $rules[Str::singular($customRelations)] = 'nullable';
            $rules[$customRelations] = 'array';
        }

        return $rules;
    }

    protected function buildDefaultRules($id = null): array
    {
        $rules = [];
        foreach ($this->target::schema() as $column) {
            if ($column['type'] === 'appends') {
                continue;
            }
            $columnRules = [$column['nullable'] || $id || $column['auto_increment'] ? 'nullable' : 'required'];
            switch($column['type']) {
                case 'string':
                case 'text': {
                    $columnRules[] = 'string';
                    if ($column['length']) {
                        $columnRules[] = 'max:'.$column['length'];
                    }
                    break;
                }
                case 'integer':
                case 'bigint':
                case 'float': 
                    $columnRules[] = 'numeric';
                    break;
                case 'datetime':
                    $columnRules[] = 'date';
                    break;
            }
            $rules[$column['column']] = join('|', $columnRules);
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
