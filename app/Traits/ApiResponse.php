<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait ApiResponse
 * 
 * Proporciona métodos estandarizados para respuestas API JSON
 * Uso: Incluir en controladores API con: use ApiResponse;
 */
trait ApiResponse
{
    /**
     * Respuesta exitosa con datos
     * 
     * @param mixed $data Datos a retornar
     * @param string $message Mensaje de éxito
     * @param int $code Código HTTP (default 200)
     * @return JsonResponse
     */
    protected function respondSuccess(
        $data = null,
        string $message = 'Success',
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'status' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ], $code);
    }

    /**
     * Respuesta de creación exitosa (201)
     * 
     * @param mixed $data Datos creados
     * @param string $message Mensaje de creación
     * @return JsonResponse
     */
    protected function respondCreated(
        $data = null,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return $this->respondSuccess($data, $message, 201);
    }

    /**
     * Respuesta sin contenido (204)
     * Usada en eliminaciones exitosas
     * 
     * @return JsonResponse
     */
    protected function respondNoContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Respuesta de error de validación (422)
     * 
     * @param array $errors Arreglo de errores de validación
     * @param string $message Mensaje de error
     * @return JsonResponse
     */
    protected function respondValidationError(
        array $errors = [],
        string $message = 'Validation failed'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => 422,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String(),
        ], 422);
    }

    /**
     * Respuesta no autenticada (401)
     * 
     * @param string $message Mensaje de error
     * @return JsonResponse
     */
    protected function respondUnauthorized(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => 401,
            'message' => $message,
            'errors' => [],
            'timestamp' => now()->toIso8601String(),
        ], 401);
    }

    /**
     * Respuesta prohibida (403)
     * 
     * @param string $message Mensaje de error
     * @return JsonResponse
     */
    protected function respondForbidden(
        string $message = 'Forbidden'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => 403,
            'message' => $message,
            'errors' => [],
            'timestamp' => now()->toIso8601String(),
        ], 403);
    }

    /**
     * Respuesta recurso no encontrado (404)
     * 
     * @param string $message Mensaje de error
     * @return JsonResponse
     */
    protected function respondNotFound(
        string $message = 'Resource not found'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => 404,
            'message' => $message,
            'errors' => [],
            'timestamp' => now()->toIso8601String(),
        ], 404);
    }

    /**
     * Respuesta con lista paginada
     * 
     * @param mixed $data Datos paginados
     * @param string $message Mensaje
     * @return JsonResponse
     */
    protected function respondPaginated(
        $data,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'has_more' => $data->hasMorePages(),
            ],
            'timestamp' => now()->toIso8601String(),
        ], 200);
    }

    /**
     * Respuesta error del servidor (500)
     * 
     * @param string $message Mensaje de error
     * @param mixed $error Detalles del error (solo en desarrollo)
     * @return JsonResponse
     */
    protected function respondInternalError(
        string $message = 'Internal server error',
        $error = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'status' => 500,
            'message' => $message,
            'errors' => [],
            'timestamp' => now()->toIso8601String(),
        ];

        if (config('app.debug') && $error) {
            $response['debug'] = [
                'exception' => get_class($error),
                'message' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
            ];
        }

        return response()->json($response, 500);
    }

    /**
     * Respuesta de error personalizada
     * 
     * @param string $message Mensaje de error
     * @param int $code Código HTTP
     * @param array $errors Arreglo de errores
     * @return JsonResponse
     */
    protected function respondError(
        string $message = 'Error',
        int $code = 400,
        array $errors = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => $code,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String(),
        ], $code);
    }

    /**
     * Respuesta Too Many Requests (429)
     * Usado para rate limiting
     * 
     * @param string $message Mensaje de error
     * @return JsonResponse
     */
    protected function respondTooManyRequests(
        string $message = 'Too many requests'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => 429,
            'message' => $message,
            'errors' => [],
            'timestamp' => now()->toIso8601String(),
        ], 429);
    }
}
