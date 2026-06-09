<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
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
use App\Policies\CorrespondenciaPolicy;
use App\Policies\DerivacionPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Mapeo de Policies
     * 
     * Define qué Policy se utiliza para cada modelo
     */
    protected $policies = [
        Correspondencia::class => CorrespondenciaPolicy::class,
        Derivacion::class => DerivacionPolicy::class,
    ];

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
        // REGISTRAR POLICIES DE AUTORIZACIÓN
        // ==============================
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

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

