<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, renombrar tabla antigua si existe
        if (Schema::hasTable('AUDITORIA')) {
            Schema::rename('AUDITORIA', 'AUDITORIA_OLD');
        }

        // Crear nueva tabla AUDITORIA mejorada
        Schema::create('AUDITORIA', function (Blueprint $table) {
            // Primary Key
            $table->id('idAuditoria');

            // Usuario que realizó la acción
            $table->unsignedBigInteger('idUsuario')->nullable();

            // Información del modelo afectado
            $table->string('modelo', 100); // Ej: Correspondencia, User, Derivacion
            $table->unsignedBigInteger('idRegistro'); // ID del registro afectado

            // Acción realizada
            $table->enum('accion', ['CREATE', 'UPDATE', 'DELETE']);

            // Datos antes y después (JSON)
            $table->longText('datosAnteriores')->nullable();
            $table->longText('datosNuevos')->nullable();

            // Información de auditoría técnica
            $table->string('ip', 45)->nullable(); // IPv4 o IPv6
            $table->string('navegador', 255)->nullable();
            $table->string('ruta', 255)->nullable(); // URL/ruta accedida

            // Timestamps
            $table->timestamp('fecha')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            // Índices para optimización
            $table->index('idUsuario');
            $table->index('modelo');
            $table->index('accion');
            $table->index('fecha');
            $table->index(['modelo', 'idRegistro']);
            $table->index(['idUsuario', 'fecha']);
            $table->index(['accion', 'fecha']);

            // Foreign key
            $table->foreign('idUsuario', 'fk_auditoria_usuario')
            ->references('id')
            ->on('users')
            ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('AUDITORIA');

        // Si existe la tabla vieja, renombrarla de vuelta
        if (Schema::hasTable('AUDITORIA_OLD')) {
            Schema::rename('AUDITORIA_OLD', 'AUDITORIA');
        }
    }
};
