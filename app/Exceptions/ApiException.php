<?php

namespace App\Exceptions;

use Exception;

/**
 * ApiException
 * Excepción base para errores de API
 */
class ApiException extends Exception
{
    protected $statusCode = 400;
    protected $errorData = [];

    public function __construct(
        string $message = '',
        int $statusCode = 400,
        array $errorData = [],
        ?Exception $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->errorData = $errorData;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorData(): array
    {
        return $this->errorData;
    }
}

/**
 * ValidationException
 * Excepción para errores de validación (422)
 */
class ValidationException extends ApiException
{
    public function __construct(array $errors = [], string $message = 'Validation failed')
    {
        parent::__construct($message, 422, $errors);
    }
}

/**
 * UnauthorizedException
 * Excepción para acceso no autorizado (401)
 */
class UnauthorizedException extends ApiException
{
    public function __construct(string $message = 'Unauthorized access')
    {
        parent::__construct($message, 401);
    }
}

/**
 * ForbiddenException
 * Excepción para acceso prohibido (403)
 */
class ForbiddenException extends ApiException
{
    public function __construct(string $message = 'Access forbidden')
    {
        parent::__construct($message, 403);
    }
}

/**
 * ResourceNotFoundException
 * Excepción para recurso no encontrado (404)
 */
class ResourceNotFoundException extends ApiException
{
    public function __construct(string $resourceName = 'Resource')
    {
        parent::__construct("{$resourceName} not found", 404);
    }
}

/**
 * ConflictException
 * Excepción para conflicto de datos (409)
 */
class ConflictException extends ApiException
{
    public function __construct(string $message = 'Resource conflict')
    {
        parent::__construct($message, 409);
    }
}

/**
 * RateLimitException
 * Excepción para límite de solicitudes (429)
 */
class RateLimitException extends ApiException
{
    public function __construct(string $message = 'Too many requests')
    {
        parent::__construct($message, 429);
    }
}
