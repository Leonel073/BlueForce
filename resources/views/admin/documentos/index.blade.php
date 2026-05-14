@php
use Illuminate\Support\Str;
@endphp
@extends('layouts.app')

@section('title', 'Gestión Documental')

@section('content')


<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-lg rounded-4 mb-4"
         style="background: linear-gradient(135deg,#0B2D59,#2E608C);">

        <div class="card-body">

            <h1 class="fw-bold text-white">

                <i class="bi bi-folder2-open"></i>

                Gestión Documental

            </h1>

            <p class="text-light mb-0">

                Administración institucional de documentos

            </p>

        </div>

    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-header text-white rounded-top-4"
             style="background-color:#0B2D59;">

            Documentos Registrados

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Cite</th>

                            <th>Asunto</th>

                            <th>Remitente</th>

                            <th>Tipo</th>

                            <th>Urgencia</th>

                            <th>Estado</th>

                            <th class="text-center">

                                Acciones

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($documentos as $doc)

                            <tr>

                                <td>

                                    {{ $doc->cite }}

                                </td>

                                <td>

                                    {{ Str::limit($doc->asunto, 60) }}

                                </td>

                                <td>

                                    {{ $doc->remitente->nombre ?? 'N/A' }}

                                </td>

                                <td>

                                    {{ $doc->tipoDocumento->nombre ?? 'N/A' }}

                                </td>

                                <td>

                                    @php

                                        $u =
                                            strtolower(
                                                $doc->urgencia->nombre ?? ''
                                            );

                                    @endphp

                                    @if(str_contains($u, 'urg') || str_contains($u, 'crit'))

                                        <span class="badge rounded-pill px-3 py-2 bg-danger">

                                            <i class="bi bi-exclamation-octagon-fill me-1"></i>

                                            {{ $doc->urgencia->nombre ?? 'N/A' }}

                                        </span>

                                    @elseif(str_contains($u, 'alta'))

                                        <span class="badge rounded-pill px-3 py-2"
                                              style="background:#ea580c;color:#fff;">

                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>

                                            {{ $doc->urgencia->nombre ?? 'N/A' }}

                                        </span>

                                    @elseif(str_contains($u, 'media') || str_contains($u, 'moder'))

                                        <span class="badge rounded-pill px-3 py-2 bg-warning text-dark">

                                            <i class="bi bi-exclamation-circle-fill me-1"></i>

                                            {{ $doc->urgencia->nombre ?? 'N/A' }}

                                        </span>

                                    @else

                                        <span class="badge rounded-pill px-3 py-2 bg-success">

                                            <i class="bi bi-check-circle-fill me-1"></i>

                                            {{ $doc->urgencia->nombre ?? 'N/A' }}

                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <span class="badge bg-primary">

                                        {{ $doc->estado->nombre ?? 'N/A' }}

                                    </span>

                                </td>

                                <td class="text-center">

                                    <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">

                                        {{-- EDITAR --}}
                                        <a href="{{ route('admin.documentos.edit', $doc->idDocumento) }}"
                                           class="btn btn-sm btn-doc btn-doc-edit"
                                           title="Editar documento">

                                            <i class="bi bi-pencil-fill"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7"
                                    class="text-center text-muted py-5">

                                    No existen documentos.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="d-flex justify-content-center mt-3">

                {{ $documentos->links() }}

            </div>

        </div>

    </div>

</div>

@endsection