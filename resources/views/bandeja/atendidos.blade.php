@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Documentos Atendidos</h1>
            
            @if($documentos->count())
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Cite</th>
                                <th>Asunto</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documentos as $doc)
                                <tr>
                                    <td>{{ $doc->cite }}</td>
                                    <td>{{ $doc->asunto }}</td>
                                    <td>{{ $doc->fecha->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $doc->estado->nombre }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('correspondencia.show', $doc->idDocumento) }}" class="btn btn-sm btn-info">Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="alert alert-info">No hay documentos atendidos.</p>
            @endif
        </div>
    </div>
</div>
@endsection
