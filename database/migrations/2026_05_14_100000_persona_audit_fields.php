<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PersonaAuditFields extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        /*
        |--------------------------------------------------------------------------
        | TABLA DEPARTAMENTO_RESPONSABLE
        |--------------------------------------------------------------------------
        | Tabla pivote para auditoría de responsables
        | Un departamento puede tener múltiples responsables en diferentes períodos
        |--------------------------------------------------------------------------
        */

        Schema::create('DEPARTAMENTO_RESPONSABLE', function (Blueprint $table) {
            $table->id('idResponsable');
            $table->unsignedBigInteger('idDepartamento');
            $table->unsignedBigInteger('idPersona');
            
            $table->dateTime('fecha_asignacion')
                  ->comment('Fecha cuando fue asignado como responsable');
            
            $table->dateTime('fecha_declinacion')
                  ->nullable()
                  ->comment('Fecha cuando fue declinado como responsable (NULL si actualmente es responsable)');
            
            $table->boolean('activo')
                  ->default(true)
                  ->comment('Indica si es actualmente responsable');
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('idDepartamento')
                  ->references('idDepartamento')
                  ->on('DEPARTAMENTO')
                  ->onDelete('cascade');
            
            $table->foreign('idPersona')
                  ->references('idPersona')
                  ->on('PERSONA')
                  ->onDelete('cascade');
            
            // Índices
            $table->index(['idDepartamento', 'activo']);
            $table->index(['idPersona', 'activo']);
        });

        /*
        |--------------------------------------------------------------------------
        | MODIFICAR TABLA PERSONA - AGREGAR CAMPOS DE AUDITORÍA
        |--------------------------------------------------------------------------
        */

        Schema::table('PERSONA', function (Blueprint $table) {
            
            // Fecha de creación del registro
            $table->dateTime('fecha_creacion')
                  ->default(\DB::raw('CURRENT_TIMESTAMP'))
                  ->after('activo')
                  ->comment('Fecha de creación de la persona en el sistema');
            
            // Fecha de deshabilitación (NULL = activo)
            $table->dateTime('fecha_deshabilitacion')
                  ->nullable()
                  ->after('fecha_creacion')
                  ->comment('Fecha de deshabilitación (borrado lógico). NULL si está activo');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        // Eliminar tabla pivote
        Schema::dropIfExists('DEPARTAMENTO_RESPONSABLE');

        // Eliminar columnas de PERSONA
        Schema::table('PERSONA', function (Blueprint $table) {
            $table->dropColumn(['fecha_creacion', 'fecha_deshabilitacion']);
        });

        Schema::enableForeignKeyConstraints();
    }
}
