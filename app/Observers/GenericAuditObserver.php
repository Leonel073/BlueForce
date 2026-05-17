<?php

namespace App\Observers;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class GenericAuditObserver
{
    /**
     * Guardar originales temporalmente
     */
    private static array $originales = [];

    /**
     * CREATED
     */
    public function created(Model $model): void
    {
        $this->registrarAuditoria($model, 'CREATE');
    }

    /**
     * UPDATING
     */
    public function updating(Model $model): void
    {
        if ($model->isDirty()) {

            self::$originales[
                class_basename($model) . '_' . $model->getKey()
            ] = $model->getOriginal();

        }
    }

    /**
     * UPDATED
     */
    public function updated(Model $model): void
    {
        if ($model->wasChanged()) {

            $this->registrarAuditoria($model, 'UPDATE');

        }
    }

    /**
     * DELETED
     */
    public function deleted(Model $model): void
    {
        $this->registrarAuditoria($model, 'DELETE');
    }

    /**
     * Registrar auditoría
     */
    private function registrarAuditoria(Model $model, string $accion): void
    {
        try {

            $idUsuario = Auth::id();

            $datosAnteriores = null;
            $datosNuevos = null;

            $clave = class_basename($model) . '_' . $model->getKey();

            /*
            |--------------------------------------------------------------------------
            | CREATE
            |--------------------------------------------------------------------------
            */

            if ($accion === 'CREATE') {

                $datosNuevos = $this->prepararDatos(
                    $model->toArray()
                );

            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE
            |--------------------------------------------------------------------------
            */

            elseif ($accion === 'UPDATE') {

                $datosAnteriores = self::$originales[$clave] ?? [];

                $datosAnteriores = $this->prepararDatos(
                    $datosAnteriores
                );

                $datosNuevos = $this->prepararDatos(
                    $model->getChanges()
                );

                unset(self::$originales[$clave]);

            }

            /*
            |--------------------------------------------------------------------------
            | DELETE
            |--------------------------------------------------------------------------
            */

            elseif ($accion === 'DELETE') {

                $datosAnteriores = $this->prepararDatos(
                    $model->toArray()
                );

            }

            /*
            |--------------------------------------------------------------------------
            | CREAR AUDITORÍA
            |--------------------------------------------------------------------------
            */

            Auditoria::create([

                'idUsuario' => $idUsuario,

                'modelo' => class_basename($model),

                'idRegistro' => $model->getKey(),

                'accion' => $accion,

                'datosAnteriores' => $datosAnteriores,

                'datosNuevos' => $datosNuevos,

                'ip' => request()->ip(),

                'navegador' => request()->userAgent(),

                'ruta' => request()->getRequestUri(),

            ]);

        } catch (\Exception $e) {

            \Log::error(
                'Error al registrar auditoría',
                [
                    'modelo' => class_basename($model),
                    'accion' => $accion,
                    'mensaje' => $e->getMessage(),
                ]
            );

        }
    }

    /**
     * Limpiar datos sensibles
     */
    private function prepararDatos(array $datos): array
    {
        $camposSensibles = [

            'password',
            'token',
            'secret',
            'api_key',
            'remember_token',

        ];

        return collect($datos)

            ->reject(function ($valor, $clave) use ($camposSensibles) {

                return in_array(
                    strtolower($clave),
                    array_map('strtolower', $camposSensibles)
                );

            })

            ->toArray();
    }
}