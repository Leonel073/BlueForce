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
            // Hacer la migración segura si columnas ya existen por migraciones previas.
            if (!Schema::hasColumn('PERSONA', 'institucion_externa')) {
                  Schema::table('PERSONA', function (Blueprint $table) {
                        $table->string('institucion_externa', 200)
                              ->nullable()
                              ->after('institucion')
                              ->comment('Institución de procedencia para personas externas (Universidad, Ministerio, Empresa, Particular, etc.)');
                  });
            }

            if (!Schema::hasColumn('PERSONA', 'fecha_creacion')) {
                  Schema::table('PERSONA', function (Blueprint $table) {
                        $table->timestamp('fecha_creacion')
                              ->nullable()
                              ->comment('Fecha de registro de la persona en el sistema');
                  });
            }

            if (!Schema::hasColumn('PERSONA', 'fecha_deshabilitacion')) {
                  Schema::table('PERSONA', function (Blueprint $table) {
                        $table->timestamp('fecha_deshabilitacion')
                              ->nullable()
                              ->comment('Fecha de deshabilitación (borrado lógico). NULL = activo');
                  });
            }

            if (!Schema::hasColumn('PERSONA', 'telefono_celular')) {
                  Schema::table('PERSONA', function (Blueprint $table) {
                        $table->string('telefono_celular', 20)
                              ->nullable()
                              ->comment('Teléfono celular de contacto');
                  });
            }

            if (!Schema::hasColumn('PERSONA', 'telefono_fijo')) {
                  Schema::table('PERSONA', function (Blueprint $table) {
                        $table->string('telefono_fijo', 20)
                              ->nullable()
                              ->comment('Teléfono fijo de contacto');
                  });
            }

            if (!Schema::hasColumn('PERSONA', 'idCargo')) {
                  Schema::table('PERSONA', function (Blueprint $table) {
                        $table->unsignedBigInteger('idCargo')
                              ->nullable()
                              ->comment('Cargo ocupado en la institución');
                  });
            }

            if (!Schema::hasColumn('PERSONA', 'idDepartamento')) {
                  Schema::table('PERSONA', function (Blueprint $table) {
                        $table->unsignedBigInteger('idDepartamento')
                              ->nullable()
                              ->comment('Departamento al que pertenece (solo internos)');
                  });
            }
    }

    public function down(): void
    {
            $columns = [
                  'institucion_externa',
                  'fecha_creacion',
                  'fecha_deshabilitacion',
                  'telefono_celular',
                  'telefono_fijo',
                  'idCargo',
                  'idDepartamento',
            ];

            foreach ($columns as $column) {
                  if (Schema::hasColumn('PERSONA', $column)) {
                        Schema::table('PERSONA', function (Blueprint $table) use ($column) {
                              $table->dropColumn($column);
                        });
                  }
            }
    }
};
