<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Traits\QueryBuilder;

/**
 * BaseController
 * 
 * Controlador base para todos los controladores API v1
 * Incluye traits para respuestas y query building
 * 
 * Uso en otros controladores:
 * class MiController extends BaseController { ... }
 */
abstract class BaseController extends Controller
{
    use ApiResponse, QueryBuilder;

    /**
     * Columnas siempre disponibles para búsqueda
     * Sobrescribir en controlador específico si es necesario
     */
    protected array $searchableColumns = [];

    /**
     * Columnas permitidas para filtrado
     * Sobrescribir en controlador específico si es necesario
     */
    protected array $filterableColumns = [];

    /**
     * Columnas permitidas para ordenamiento
     * Sobrescribir en controlador específico si es necesario
     */
    protected array $sortableColumns = [];

    /**
     * Obtiene las opciones de consulta desde el request
     * 
     * @return array
     */
    protected function getOptions(): array
    {
        return $this->getQueryOptions();
    }

    /**
     * Construye una consulta con todas las opciones
     * 
     * @param mixed $query
     * @return mixed
     */
    protected function buildFullQuery($query)
    {
        return $this->buildQuery(
            $query,
            $this->getOptions(),
            $this->searchableColumns,
            $this->filterableColumns,
            $this->sortableColumns
        );
    }
}
