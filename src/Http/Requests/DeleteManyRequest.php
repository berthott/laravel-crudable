<?php

namespace berthott\Crudable\Http\Requests;

use berthott\Crudable\Exceptions\ValidationException;
use Facades\berthott\Crudable\Services\CrudableService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class DeleteManyRequest extends FormRequest
{
    private string $target;

    public function __construct()
    {
        $this->target = CrudableService::getTarget();
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * @api
     */
    public function rules(): array
    {
        return [
            'ids' => 'required',
            'ids.*' => 'exists:'.$this->target::entityTableName().',id',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($this->validator->errors()->messages()));
    }
}
