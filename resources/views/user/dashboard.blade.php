@extends('layouts.app')

@section('title', 'Dashboard Usuario')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h2 class="fw-bold text-white mb-1">

                <i class="bi bi-person-circle"></i>

                Bienvenido al Sistema

            </h2>

            <p class="text-light mb-0">

                Gestión institucional de correspondencia y flujo documental

            </p>

        </div>

    </div>

    {{-- ACCESOS RÁPIDOS --}}
    <div class="row">

        {{-- MI BANDEJA --}}
        <div class="col-md-4 mb-4">

            <div class="card border-0 shadow-sm rounded-4 h-100">

                <div class="card-body text-center p-4">

                    <div class="mb-3">

                        <i class="bi bi-inbox-fill fs-1 text-primary"></i>

                    </div>

                    <h5 class="fw-bold">

                        Mi Bandeja

                    </h5>

                    <p class="text-muted small">

                        Documentos en flujo y pendientes.

                    </p>

                    <a href="{{ route('envios.bandeja') }}"
                       class="btn text-white rounded-4 px-4"
                       style="background-color:#0B2D59;">

                        Ingresar

                    </a>

                </div>

            </div>

        </div>

        {{-- ENVIADOS --}}
        <div class="col-md-4 mb-4">

            <div class="card border-0 shadow-sm rounded-4 h-100">

                <div class="card-body text-center p-4">

                    <div class="mb-3">

                        <i class="bi bi-send-fill fs-1 text-success"></i>

                    </div>

                    <h5 class="fw-bold">

                        Enviados

                    </h5>

                    <p class="text-muted small">

                        Historial de documentos enviados.

                    </p>

                    <a href="{{ route('envios.index') }}"
                       class="btn btn-success rounded-4 px-4">

                        Ver Documentos

                    </a>

                </div>

            </div>

        </div>

        {{-- DOCUMENTOS --}}
        <div class="col-md-4 mb-4">

            <div class="card border-0 shadow-sm rounded-4 h-100">

                <div class="card-body text-center p-4">

                    <div class="mb-3">

                        <i class="bi bi-file-earmark-text-fill fs-1 text-danger"></i>

                    </div>

                    <h5 class="fw-bold">

                        Correspondencia

                    </h5>

                    <p class="text-muted small">

                        Consulta y seguimiento documental.

                    </p>

                    <a href="{{ route('correspondencia.index') }}"
                       class="btn btn-danger rounded-4 px-4">

                        Ver Correspondencia

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection