<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Agrega campo tipo_persona a PERSONA para distinguir trabajadores de externos.
     * Solo los trabajadores pueden tener cuenta de usuario.
     * El campo tipo original (INTERNO/EXTERNO) NO se modifica para mantener
     * compatibilidad con toda la lógica existente del sistema.
     */
    public function up(): void
    {
        Schema::table('PERSONA', function (Blueprint $table) {
            $table->enum('tipo_persona', ['trabajador', 'externo'])
                  ->nullable()
                  ->after('tipo')
                  ->comment('Clasificación para acceso: trabajador (puede tener usuario) o externo (no puede)');
        });

        // ============================================================
        // MIGRACIÓN DE DATOS EXISTENTES
        // ============================================================

        // 1. Personas que ya tienen usuario asignado → trabajador (prioridad máxima)
        DB::statement("
            UPDATE PERSONA p
            INNER JOIN users u ON u.idPersona = p.idPersona
            SET p.tipo_persona = 'trabajador'
        ");

        // 2. Personas INTERNAS con departamento asignado que no tienen usuario → trabajador
        DB::statement("
            UPDATE PERSONA
            SET tipo_persona = 'trabajador'
            WHERE tipo = 'INTERNO'
              AND idDepartamento IS NOT NULL
              AND tipo_persona IS NULL
        ");

        // 3. Todo lo demás → externo
        DB::statement("
            UPDATE PERSONA
            SET tipo_persona = 'externo'
            WHERE tipo_persona IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('PERSONA', function (Blueprint $table) {
            $table->dropColumn('tipo_persona');
        });
    }
};
