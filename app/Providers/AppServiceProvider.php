<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Observers\GenericAuditObserver;
use App\Models\{
    Correspondencia,
    Derivacion,
    User,
    Departamento,
    Persona,
    EstadoDocumento,
    NivelUrgencia,
    TipoDocumento,
    Seguimiento
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // ==============================
        // REGISTRAR OBSERVERS DE AUDITORÍA
        // ==============================

        // Registrar el observer genérico para todos los modelos importantes
        $modelos = [
            Correspondencia::class,
            Derivacion::class,
            User::class,
            Departamento::class,
            Persona::class,
            EstadoDocumento::class,
            NivelUrgencia::class,
            TipoDocumento::class,
            Seguimiento::class,
        ];

        foreach ($modelos as $modelo) {
            $modelo::observe(GenericAuditObserver::class);
        }
    }
}
