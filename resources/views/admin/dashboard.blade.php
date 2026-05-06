@extends('layouts.app')

@section('content')
    <h3 class="mb-4">Panel de Administrador</h3>
    
    <div class="card shadow-sm border-0 p-4">
        <h4>¡Bienvenido Administrador {{ Auth::user()->name }}!</h4>
        <p class="text-muted">Aquí verás el módulo de gestión de usuarios en el futuro.</p>
    </div>
@endsection