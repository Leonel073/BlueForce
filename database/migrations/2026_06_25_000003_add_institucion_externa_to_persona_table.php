<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega soporte para instituciones externas en personas externas.
     * 
     * Objetivo: Permitir registrar de dónde provienen las personas externas
     * (Universidad, Ministerio, Empresa, Particular, etc.)
     * 
     * Campo "institucion" existente: usado para INTERNOS (histórico)
     * Campo "institucion_externa": nuevo, para EXTERNOS solamente
     */
    public function up(): void
    {
        Schema::table('PERSONA', function (Blueprint $table) {
            // Agregar soporte para institución de personas externas
            $table->string('institucion_externa', 200)
                  ->nullable()
                  ->after('institucion')
                  ->comment('Institución de procedencia para personas externas (Universidad, Ministerio, Empresa, Particular, etc.)');

            // Agregar fechas de control de ciclo de vida
            $table->timestamp('fecha_creacion')
                  ->nullable()
                  ->after('institucion_externa')
                  ->comment('Fecha de registro de la persona en el sistema');

            $table->timestamp('fecha_deshabilitacion')
                  ->nullable()
                  ->after('fecha_creacion')
                  ->comment('Fecha de deshabilitación (borrado lógico). NULL = activo');

            // Agregar teléfono celular para mejor contactabilidad
            $table->string('telefono_celular', 20)
                  ->nullable()
                  ->after('fecha_deshabilitacion')
                  ->comment('Teléfono celular de contacto');

            // Agregar teléfono fijo
            $table->string('telefono_fijo', 20)
                  ->nullable()
                  ->after('telefono_celular')
                  ->comment('Teléfono fijo de contacto');

            // Agregar FK para cargo (relación m:1)
            $table->unsignedBigInteger('idCargo')
                  ->nullable()
                  ->after('telefono_fijo')
                  ->comment('Cargo ocupado en la institución');

            // Agregar FK para departamento (relación m:1)
            $table->unsignedBigInteger('idDepartamento')
                  ->nullable()
                  ->after('idCargo')
                  ->comment('Departamento al que pertenece (solo internos)');
        });
    }

    public function down(): void
    {
        Schema::table('PERSONA', function (Blueprint $table) {
            $table->dropColumn([
                'institucion_externa',
                'fecha_creacion',
                'fecha_deshabilitacion',
                'telefono_celular',
                'telefono_fijo',
                'idCargo',
                'idDepartamento',
            ]);
        });
    }
};
