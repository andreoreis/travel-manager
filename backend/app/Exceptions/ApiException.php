<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class ApiException extends Exception
{
    protected $statusCode;

    public function __construct(string $message, int $statusCode = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
    }

    public function render(): JsonResponse
    {
        return response()->json(['error' => $this->getMessage()], $this->statusCode);
    }
}
