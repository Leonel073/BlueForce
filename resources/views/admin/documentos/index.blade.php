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

                            <th>Activo</th>

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

                                    <span class="badge bg-danger">

                                        {{ $doc->urgencia->nombre ?? 'N/A' }}

                                    </span>

                                </td>

                                <td>

                                    <span class="badge bg-primary">

                                        {{ $doc->estado->nombre ?? 'N/A' }}

                                    </span>

                                </td>

                                <td>

                                    @if($doc->activo)

                                        <span class="badge bg-success">

                                            Activo

                                        </span>

                                    @else

                                        <span class="badge bg-danger">

                                            Archivado

                                        </span>

                                    @endif

                                </td>

                                <td class="text-center">

                                    <div class="btn-group">

                                        {{-- EDITAR --}}
                                        <a href="{{ route('admin.documentos.edit', $doc->idDocumento) }}"
                                           class="btn btn-warning btn-sm">

                                            <i class="bi bi-pencil-fill"></i>

                                        </a>

                                        {{-- TOGGLE --}}
                                        <form action="{{ route('admin.documentos.toggle', $doc->idDocumento) }}"
                                              method="POST">

                                            @csrf
                                            @method('PUT')

                                            <button type="submit"
                                                    class="btn btn-sm {{ $doc->activo ? 'btn-danger' : 'btn-success' }}">

                                                <i class="bi {{ $doc->activo ? 'bi-archive-fill' : 'bi-arrow-clockwise' }}"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
                                    class="text-center text-muted py-5">

                                    No existen documentos.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection