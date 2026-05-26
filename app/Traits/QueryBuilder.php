<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait QueryBuilder
 * 
 * Proporciona métodos para filtrado, búsqueda, ordenamiento y paginación
 * Uso: Agregar al controlador con: use QueryBuilder;
 */
trait QueryBuilder
{
    /**
     * Aplica filtros a la consulta
     * 
     * @param Builder $query
     * @param array $filters Filtros a aplicar
     * @param array $filterableColumns Columnas permitidas para filtrar
     * @return Builder
     */
    public function applyFilters(
        Builder $query,
        array $filters,
        array $filterableColumns
    ): Builder {
        foreach ($filters as $column => $value) {
            if (in_array($column, $filterableColumns) && $value !== null) {
                // Soporta operadores: gt, gte, lt, lte, ne
                if (is_array($value) && isset($value['operator'], $value['value'])) {
                    match ($value['operator']) {
                        'gt' => $query->where($column, '>', $value['value']),
                        'gte' => $query->where($column, '>=', $value['value']),
                        'lt' => $query->where($column, '<', $value['value']),
                        'lte' => $query->where($column, '<=', $value['value']),
                        'ne' => $query->where($column, '!=', $value['value']),
                        default => $query->where($column, $value),
                    };
                } else {
                    $query->where($column, $value);
                }
            }
        }

        return $query;
    }

    /**
     * Aplica búsqueda de texto en múltiples columnas
     * 
     * @param Builder $query
     * @param string $search Término de búsqueda
     * @param array $searchableColumns Columnas donde buscar
     * @return Builder
     */
    public function applySearch(
        Builder $query,
        string $search,
        array $searchableColumns
    ): Builder {
        if (empty($search)) {
            return $query;
        }

        $search = "%{$search}%";

        return $query->where(function ($q) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $q->orWhere($column, 'LIKE', $search);
            }
        });
    }

    /**
     * Aplica ordenamiento a la consulta
     * 
     * @param Builder $query
     * @param string|array $sort Campo(s) para ordenar
     * @param array $sortableColumns Columnas permitidas para ordenar
     * @return Builder
     */
    public function applySort(
        Builder $query,
        $sort,
        array $sortableColumns
    ): Builder {
        if (empty($sort)) {
            return $query;
        }

        // Si es string, convertir a array
        if (is_string($sort)) {
            $sort = explode(',', $sort);
        }

        foreach ($sort as $sortField) {
            $sortField = trim($sortField);
            $direction = 'asc';

            // Si comienza con -, es descendente
            if (str_starts_with($sortField, '-')) {
                $direction = 'desc';
                $sortField = ltrim($sortField, '-');
            }

            if (in_array($sortField, $sortableColumns)) {
                $query->orderBy($sortField, $direction);
            }
        }

        return $query;
    }

    /**
     * Aplica rango de fechas
     * 
     * @param Builder $query
     * @param string $dateColumn Columna de fecha
     * @param string|null $from Fecha desde (YYYY-MM-DD)
     * @param string|null $to Fecha hasta (YYYY-MM-DD)
     * @return Builder
     */
    public function applyDateRange(
        Builder $query,
        string $dateColumn,
        ?string $from,
        ?string $to
    ): Builder {
        if ($from) {
            $query->whereDate($dateColumn, '>=', $from);
        }

        if ($to) {
            $query->whereDate($dateColumn, '<=', $to);
        }

        return $query;
    }

    /**
     * Aplica estado activo/inactivo
     * 
     * @param Builder $query
     * @param bool|null $active
     * @return Builder
     */
    public function applyActive(
        Builder $query,
        ?bool $active = true
    ): Builder {
        if ($active !== null) {
            $query->where('activo', $active ? 1 : 0);
        }

        return $query;
    }

    /**
     * Combina todas las opciones de consulta
     * 
     * @param Builder $query
     * @param array $options Opciones de consulta:
     *                       - search: término de búsqueda
     *                       - filters: filtros adicionales
     *                       - sort: campos para ordenar
     *                       - page: número de página
     *                       - per_page: cantidad por página
     *                       - with: relaciones eager loading
     * @param array $searchableColumns Columnas para búsqueda
     * @param array $filterableColumns Columnas para filtrado
     * @param array $sortableColumns Columnas para ordenamiento
     * @return mixed Builder o LengthAwarePaginator
     */
    public function buildQuery(
        Builder $query,
        array $options,
        array $searchableColumns = [],
        array $filterableColumns = [],
        array $sortableColumns = []
    ) {
        // Eager loading de relaciones
        if (!empty($options['with'])) {
            $relations = is_string($options['with']) 
                ? explode(',', $options['with']) 
                : $options['with'];
            $query->with($relations);
        }

        // Búsqueda
        if (!empty($options['search'])) {
            $this->applySearch($query, $options['search'], $searchableColumns);
        }

        // Filtros
        if (!empty($options['filters'])) {
            $this->applyFilters($query, $options['filters'], $filterableColumns);
        }

        // Ordenamiento
        if (!empty($options['sort'])) {
            $this->applySort($query, $options['sort'], $sortableColumns);
        }

        // Estado activo
        if (isset($options['active'])) {
            $this->applyActive($query, (bool)$options['active']);
        }

        // Paginación
        $perPage = min((int)($options['per_page'] ?? 10), 100);
        $page = (int)($options['page'] ?? 1);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Obtiene opciones de consulta desde request
     * 
     * @return array
     */
    public function getQueryOptions(): array
    {
        return [
            'search' => request()->input('search'),
            'filters' => request()->input('filters', []),
            'sort' => request()->input('sort'),
            'page' => request()->input('page', 1),
            'per_page' => request()->input('per_page', 10),
            'with' => request()->input('with'),
            'active' => request()->input('active'),
        ];
    }
}
