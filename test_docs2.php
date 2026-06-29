<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$docs = App\Models\Correspondencia::whereHas('ultimaDerivacion', function($q) {
    $q->where('idUsuarioAsignado', 2);
})->get();

echo "Docs for user 2 via ultimaDerivacion whereHas:\n";
foreach($docs as $doc) {
    echo "Doc ID: " . $doc->idDocumento . "\n";
}

$docs2 = App\Models\Correspondencia::whereHas('ultimaDerivacion')->get();
echo "\nDocs with ultimaDerivacion:\n";
foreach($docs2 as $doc) {
    echo "Doc ID: " . $doc->idDocumento . "\n";
}
