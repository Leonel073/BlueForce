<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$docs = App\Models\Correspondencia::with(['estado', 'ultimaDerivacion', 'derivaciones'])->orderByDesc('idDocumento')->take(5)->get();

foreach($docs as $doc) {
    echo "Doc ID: " . $doc->idDocumento . "\n";
    echo "Estado: " . ($doc->estado ? $doc->estado->nombre : 'NULL') . "\n";
    echo "Ultima derivacion asignado: " . ($doc->ultimaDerivacion ? $doc->ultimaDerivacion->idUsuarioAsignado : 'NULL') . "\n";
    echo "Derivaciones count: " . $doc->derivaciones->count() . "\n";
    echo "--------------------------\n";
}
