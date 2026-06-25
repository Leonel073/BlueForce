<?php
// Test para auditar validación de contraseña

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== AUDITORÍA: VALIDACIÓN DE CONTRASEÑA ===\n\n";

$password = 'SefyX+8Q@u32';

echo "Contraseña a validar: '$password'\n";
echo "Longitud: " . strlen($password) . " caracteres\n\n";

// Análisis carácter por carácter
echo "Análisis detallado:\n";
preg_match_all('/[a-z]/', $password, $minusculas);
preg_match_all('/[A-Z]/', $password, $mayusculas);
preg_match_all('/\d/', $password, $numeros);
preg_match_all('/[@$!%*?&]/', $password, $especiales);
preg_match_all('/[^a-zA-Z0-9@$!%*?&]/', $password, $invalidos);

echo "├─ Minúsculas: " . count($minusculas[0]) . " (" . implode('', $minusculas[0]) . ")\n";
echo "├─ Mayúsculas: " . count($mayusculas[0]) . " (" . implode('', $mayusculas[0]) . ")\n";
echo "├─ Números: " . count($numeros[0]) . " (" . implode('', $numeros[0]) . ")\n";
echo "├─ Especiales permitidos (@$!%*?&): " . count($especiales[0]) . " (" . implode('', $especiales[0]) . ")\n";
echo "└─ Caracteres NO permitidos: " . count($invalidos[0]) . " (" . implode('', $invalidos[0]) . ")\n\n";

// Validación min:12
echo "1. Validación min:12\n";
echo "   Resultado: " . (strlen($password) >= 12 ? "✅ PASA" : "❌ FALLA") . "\n\n";

// Validación regex de StoreUsuarioRequest
$regex1 = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[a-zA-Z\d@$!%*?&]+$/';
echo "2. Validación regex (StoreUsuarioRequest):\n";
echo "   Patrón: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@\\$!%*?&])[a-zA-Z\\d@\\$!%*?&]+$/\n";
echo "   Resultado: " . (preg_match($regex1, $password) ? "✅ PASA" : "❌ FALLA") . "\n\n";

// Desglose del regex
echo "   Desglose del regex:\n";
echo "   ├─ (?=.*[a-z]): Al menos una minúscula: " . (preg_match('/(?=.*[a-z])/', $password) ? "✅" : "❌") . "\n";
echo "   ├─ (?=.*[A-Z]): Al menos una mayúscula: " . (preg_match('/(?=.*[A-Z])/', $password) ? "✅" : "❌") . "\n";
echo "   ├─ (?=.*\\d): Al menos un número: " . (preg_match('/(?=.*\d)/', $password) ? "✅" : "❌") . "\n";
echo "   ├─ (?=.*[@\\$!%*?&]): Al menos especial: " . (preg_match('/(?=.*[@$!%*?&])/', $password) ? "✅" : "❌") . "\n";
echo "   └─ [a-zA-Z\\d@\\$!%*?&]+: Solo estos caracteres: " . (preg_match('/^[a-zA-Z\d@$!%*?&]+$/', $password) ? "✅" : "❌") . "\n\n";

// Simulación de validación Laravel
echo "3. Simulación de validación Laravel (StoreUsuarioRequest):\n";
$validator = \Illuminate\Support\Facades\Validator::make(
    ['password' => $password],
    [
        'password' => [
            'required',
            'string',
            'min:12',
            'confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[a-zA-Z\d@$!%*?&]+$/',
        ],
    ]
);

// Nota: La validación 'confirmed' requiere password_confirmation, así que omitimos ese test
$validator2 = \Illuminate\Support\Facades\Validator::make(
    ['password' => $password],
    [
        'password' => [
            'required',
            'string',
            'min:12',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[a-zA-Z\d@$!%*?&]+$/',
        ],
    ]
);

if ($validator2->passes()) {
    echo "   ✅ PASA (sin validación 'confirmed')\n";
} else {
    echo "   ❌ FALLA\n";
    foreach ($validator2->errors()->all() as $error) {
        echo "      Error: $error\n";
    }
}

// Validación del método update (que es más relajada)
echo "\n4. Validación en UsuarioController@update:\n";
echo "   Regla: min:12|confirmed\n";
$validator3 = \Illuminate\Support\Facades\Validator::make(
    ['password' => $password],
    ['password' => 'min:12|confirmed']
);

if ($validator3->passes()) {
    echo "   ✅ PASA (sin validación 'confirmed')\n";
} else {
    echo "   ❌ FALLA\n";
    foreach ($validator3->errors()->all() as $error) {
        echo "      Error: $error\n";
    }
}

echo "\n=== CONCLUSIÓN ===\n";
echo "La contraseña '$password' cumple:\n";
echo "✅ Mínimo 12 caracteres (" . strlen($password) . ")\n";
echo "✅ Al menos una minúscula\n";
echo "✅ Al menos una mayúscula\n";
echo "✅ Al menos un número\n";
echo "✅ Al menos un carácter especial: + (¿problema?)\n\n";

echo "RESULTADO: El caracter '+' NO está en la lista permitida [@\$!%*?&]\n";
echo "La contraseña será RECHAZADA por el regex de StoreUsuarioRequest\n";
echo "Caracteres permitidos: @ \$ ! % * ? &\n";
echo "Caracteres NO permitidos: . + - _ ^ ~ ` | \\ () {} [] <> / : ; , etc.\n";
