<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Auditoria;
use App\Helpers\AuditoriaHelper;

class VerifyAuditoriaSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auditoria:verify';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Verificar que el sistema de auditoría esté correctamente configurado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("\n" . str_repeat("=", 60));
        $this->info("VERIFICACIÓN DEL SISTEMA DE AUDITORÍA");
        $this->info(str_repeat("=", 60) . "\n");

        $errores = 0;

        // Verificar tabla
        $this->info("1️⃣  Verificando tabla AUDITORIA...");
        if ($this->verificarTabla()) {
            $this->line("   ✅ Tabla existe");
        } else {
            $this->error("   ❌ Tabla NO existe - Ejecutar: php artisan migrate");
            $errores++;
        }

        // Verificar modelo
        $this->info("\n2️⃣  Verificando modelo Auditoria...");
        if ($this->verificarModelo()) {
            $this->line("   ✅ Modelo cargado correctamente");
        } else {
            $this->error("   ❌ Error cargando modelo");
            $errores++;
        }

        // Verificar helper
        $this->info("\n3️⃣  Verificando AuditoriaHelper...");
        if ($this->verificarHelper()) {
            $this->line("   ✅ Helper funcionando");
        } else {
            $this->error("   ❌ Error con helper");
            $errores++;
        }

        // Verificar rutas
        $this->info("\n4️⃣  Verificando rutas...");
        if ($this->verificarRutas()) {
            $this->line("   ✅ Rutas configuradas");
        } else {
            $this->error("   ❌ Rutas incompletas");
            $errores++;
        }

        // Estadísticas
        $this->info("\n5️⃣  Estadísticas de auditoría:");
        $this->mostrarEstadisticas();

        // Resumen
        $this->info("\n" . str_repeat("=", 60));
        if ($errores === 0) {
            $this->info("✅ SISTEMA DE AUDITORÍA CONFIGURADO CORRECTAMENTE");
            $this->info("\n📍 Acceso: http://localhost/admin/auditoria");
        } else {
            $this->error("❌ PROBLEMAS ENCONTRADOS: $errores");
        }
        $this->info(str_repeat("=", 60) . "\n");

        return $errores === 0 ? 0 : 1;
    }

    private function verificarTabla(): bool
    {
        try {
            $exists = \DB::getSchemaBuilder()->hasTable('AUDITORIA');
            if ($exists) {
                $columns = \DB::getSchemaBuilder()->getColumnListing('AUDITORIA');
                $camposRequeridos = ['idAuditoria', 'idUsuario', 'modelo', 'accion'];
                foreach ($camposRequeridos as $campo) {
                    if (!in_array($campo, $columns)) {
                        return false;
                    }
                }
            }
            return $exists;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function verificarModelo(): bool
    {
        try {
            $modelo = new Auditoria();
            return $modelo->getTable() === 'AUDITORIA';
        } catch (\Exception $e) {
            return false;
        }
    }

    private function verificarHelper(): bool
    {
        try {
            $resumen = AuditoriaHelper::obtenerResumen(dias: 7);
            return isset($resumen['total']);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function verificarRutas(): bool
    {
        $rutas = [
            'auditoria.index',
            'auditoria.show',
            'auditoria.estadisticas',
        ];

        foreach ($rutas as $ruta) {
            try {
                route($ruta);
            } catch (\Exception $e) {
                return false;
            }
        }
        return true;
    }

    private function mostrarEstadisticas(): void
    {
        try {
            $total = Auditoria::count();
            $resumen = AuditoriaHelper::obtenerResumen(dias: 7);

            $this->line("   Total de registros: " . $total);
            $this->line("   Últimos 7 días: " . $resumen['total']);
            $this->line("   - Creaciones: " . $resumen['creaciones']);
            $this->line("   - Actualizaciones: " . $resumen['actualizaciones']);
            $this->line("   - Eliminaciones: " . $resumen['eliminaciones']);
            $this->line("   - Usuarios activos: " . $resumen['usuarios_activos']);

            $modelos = Auditoria::distinct()->pluck('modelo');
            if ($modelos->count() > 0) {
                $this->line("   Modelos auditados: " . implode(", ", $modelos->toArray()));
            }
        } catch (\Exception $e) {
            $this->error("   Error al obtener estadísticas: " . $e->getMessage());
        }
    }
}
