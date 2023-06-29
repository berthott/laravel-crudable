<?php

namespace berthott\Crudable\Http\Requests;

use berthott\Crudable\Exceptions\ValidationException;
use Facades\berthott\Crudable\Services\CrudableService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    private Model $instance;

    private string $target;

    public function __construct()
    {
        $this->target = CrudableService::getTarget();
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * * appended fields won't have any rules and will be excluded from the request
     * * nullable and auto_increment columns are nullable
     * * all other columns are required for storing, and nullable for updating
     * * unique columns need to be unique
     * * string and text columns will have a max length validation according to their length
     * * integer, bigint and float columns need to be numeric
     * * datetime column need to contain a date
     * * attachables need to be an integer id or array of ids that exist on the related table
     * * creatables can be any data and nullable
     * * custom relations can be any data and nullable
     * 
     * @api
     */
    public function rules(): array
    {
        return array_merge(
            array_fill_keys($this->getInstance()->getFillable(), 'nullable'),  // default fillable rules
            $this->buildDefaultRules($this->getPrimaryId()), // default rules from schema
            $this->buildAttachableRules(),  // default attachables rules
            $this->buildCreatableRules(),  // default creatables rules
            $this->buildCustomRelationRules(),  // default custom relation rules
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

    /**
     * Build the attachable rules.
     * 
     * An attachable needs to be an integer id or array of ids that exist on the related table.
     */
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

    /**
     * Build the creatable rules.
     * 
     * Creatables can be any data and nullable.
     */
    protected function buildCreatableRules(): array
    {
        $rules = [];
        foreach (array_keys($this->target::creatables()) as $creatable) {
            $isMany = Str::plural($creatable) === $creatable;
            $rules[Str::singular($creatable)] = 'nullable';
            if ($isMany) {
                $rules[$creatable] = 'array';
            } else {
                $rules[Str::singular($creatable).'_id'] = 'nullable';
            }
        }

        return $rules;
    }

    /**
     * Build custom relation rules.
     * 
     * Custom relations can be any data and nullable.
     */
    protected function buildCustomRelationRules(): array
    {
        $rules = [];
        foreach (array_keys($this->target::customRelations()) as $customRelations) {
            $rules[Str::singular($customRelations)] = 'nullable';
            $rules[$customRelations] = 'array';
        }

        return $rules;
    }

    /**
     * Build the default rules.
     * 
     * * appended fields won't have any rules and will be excluded from the request
     * * nullable and auto_increment columns are nullable
     * * all other columns are required for storing, and nullable for updating
     * * unique columns need to be unique
     * * string and text columns will have a max length validation according to their length
     * * integer, bigint and float columns need to be numeric
     * * datetime column need to contain a date
     */
    protected function buildDefaultRules($id = null): array
    {
        $rules = [];
        foreach ($this->target::schema() as $column) {
            if ($column['type'] === 'appends') {
                continue;
            }
            $columnRules = [$column['nullable'] || $id || $column['auto_increment'] ? 'nullable' : 'required'];
            if ($column['unique']) {
                $columnRules[] = Rule::unique($this->getTableName())->ignore($id);
            }
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
            $rules[$column['column']] = $columnRules;
        }

        return $rules;
    }

    /**
     * Get the singleton.
     */
    protected function getInstance(): Model
    {
        if (!isset($this->instance)) {
            $this->instance = new $this->target();
        }

        return $this->instance;
    }

    /**
     * The entity table name of the model.
     */
    protected function getTableName(): string
    {
        return $this->getInstance()->getTable($this->target);
    }

    /**
     * The single name of the model.
     */
    protected function getSingularName(): string
    {
        return Str::singular($this->getInstance()->getTable($this->target));
    }

    /**
     * Is the current request an update request
     */
    protected function isUpdate(): bool
    {
        return !empty($this->route($this->getSingularName()));
    }

    /**
     * Get the primary ID string.
     */
    protected function getPrimaryId(): mixed
    {
        return $this->isUpdate() ? $this->route($this->getSingularName()) : null;
    }
}
