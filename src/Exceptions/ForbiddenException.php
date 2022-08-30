<?php

namespace berthott\Crudable\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ForbiddenException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct()
    {
        parent::__construct('The action is forbidden.');
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(/* Request $request */): JsonResponse
    {
        return response()->json(['error' => 'Unauthorized.'], 403);
    }
}
