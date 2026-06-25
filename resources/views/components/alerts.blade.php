{{-- Sistema de Alertas Centralizado --}}
<div class="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000; max-width: 400px;">

    {{-- ALERTA DE ÉXITO --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-lg rounded-4 mb-2" role="alert" style="animation: slideIn 0.3s ease;">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>¡Éxito!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- ALERTA DE ERROR --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-lg rounded-4 mb-2" role="alert" style="animation: slideIn 0.3s ease;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Error:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- ALERTA DE ADVERTENCIA --}}
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-lg rounded-4 mb-2" role="alert" style="animation: slideIn 0.3s ease;">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <strong>Advertencia:</strong> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    {{-- ALERTA DE INFORMACIÓN --}}
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show border-0 shadow-lg rounded-4 mb-2" role="alert" style="animation: slideIn 0.3s ease;">
            <i class="bi bi-info-circle-fill me-2"></i>
            <strong>Información:</strong> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

</div>

<style>
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .alert-container .alert {
        margin-bottom: 10px;
    }

    @media (max-width: 576px) {
        .alert-container {
            left: 10px !important;
            right: 10px !important;
            max-width: none !important;
        }
    }
</style>
