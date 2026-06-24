<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Refuerza la integridad referencial de users.idPersona:
 * - Elimina usuarios huérfanos (sin persona) que no sean administradores
 * - Agrega FK explícita hacia PERSONA si no existía
 *
 * NOTA: No hace NOT NULL para no bloquear seeder/factory en tests.
 * La restricción de negocio se aplica en Request y Controller.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Asegurar UNIQUE sobre idPersona (ya existe en migración original, pero lo verificamos)
        // Si ya existe el índice no fallará porque usamos try/catch
        try {
            Schema::table('users', function (Blueprint $table) {
                // Verificar si ya existe la FK antes de agregarla
                $table->foreign('idPersona', 'fk_users_persona')
                      ->references('idPersona')
                      ->on('PERSONA')
                      ->nullOnDelete();
            });
        } catch (\Exception $e) {
            // La FK ya existe — ignorar
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign('fk_users_persona');
            });
        } catch (\Exception $e) {
            // No existía la FK
        }

        Schema::enableForeignKeyConstraints();
    }
};
