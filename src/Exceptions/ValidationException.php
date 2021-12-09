<?php

namespace berthott\Crudable\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ValidationException extends Exception
{
    /**
     * The error.
     */
    private array $error;

    /**
     * Create a new exception instance.
     */
    public function __construct(array $error)
    {
        parent::__construct('The Validation failed.');

        $this->error = $error;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(/* Request $request */): JsonResponse
    {
        return response()->json(['errors' => $this->error], 422);
    }
}
