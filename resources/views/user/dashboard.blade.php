@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<h2>Bienvenido 👋</h2>

<div class="row mt-4">

    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h5>Enviadas</h5>
            <a href="{{ route('enviadas') }}" class="btn btn-primary">Ver</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h5>Recibidas</h5>
            <a href="{{ route('recibidas') }}" class="btn btn-primary">Ver</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h5>Documentos</h5>
            <a href="{{ route('documentos.show') }}" class="btn btn-primary">Ver</a>
        </div>
    </div>

</div>

@endsection