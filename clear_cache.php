<?php
// Script para limpiar cache
$dir = __DIR__ . '/storage/framework/views';
$files = glob($dir . '/*.php');
foreach ($files as $file) {
    if (basename($file) !== '.gitignore' && is_file($file)) {
        unlink($file);
    }
}
echo "Cache limpiado\n";
?>