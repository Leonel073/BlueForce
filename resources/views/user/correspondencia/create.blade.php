@extends('layouts.app')

@section('title', 'Nuevo Documento')

@section('content')

<div class="container">

    <h3 class="mb-4 fw-bold">📄 Registrar Documento</h3>

    <!-- ERRORES -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">

            <form action="{{ route('documento.store') }}" method="POST">
                @csrf

                <div class="row">

                    <!-- CITE -->
                    <div class="col-md-6 mb-3">
                        <label>CITE</label>
                        <input type="text" name="cite" class="form-control" required>
                    </div>

                    <!-- FECHA -->
                    <div class="col-md-6 mb-3">
                        <label>Fecha</label>
                        <input type="text" class="form-control" value="{{ now() }}" disabled>
                    </div>

                    <!-- ASUNTO -->
                    <div class="col-md-12 mb-3">
                        <label>Asunto</label>
                        <textarea name="asunto" class="form-control" required></textarea>
                    </div>

                    <!-- TIPO -->
                    <div class="col-md-4 mb-3">
                        <label>Tipo Documento</label>
                        <select name="idTipoDocumento" class="form-select">
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->idTipoDocumento }}">
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- ESTADO -->
                    <div class="col-md-4 mb-3">
                        <label>Estado</label>
                        <select name="idEstado" class="form-select">
                            @foreach($estados as $estado)
                                <option value="{{ $estado->idEstado }}">
                                    {{ $estado->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- URGENCIA -->
                    <div class="col-md-4 mb-3">
                        <label>Urgencia</label>
                        <select name="idUrgencia" class="form-select">
                            @foreach($urgencias as $urgencia)
                                <option value="{{ $urgencia->idUrgencia }}">
                                    {{ $urgencia->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- REMITENTE -->
                    <div class="col-md-6 mb-3">
                        <label>Remitente</label>
                        <select name="idRemitente" class="form-select">
                            @foreach($personas as $p)
                                <option value="{{ $p->idPersona }}">
                                    {{ $p->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">Guardar Documento</button>
                    <a href="{{ route('documentos') }}" class="btn btn-secondary">Volver</a>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection