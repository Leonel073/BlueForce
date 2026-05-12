<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificacionesBaseDatos extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        // ==============================
        // MODIFICAR TABLA PERSONA
        // ==============================
        Schema::table('PERSONA', function (Blueprint $table) {
            // Agregar teléfono celular (obligatorio)
            $table->string('telefono_celular', 20)->after('correo');
            
            // Agregar teléfono fijo (opcional)
            $table->string('telefono_fijo', 20)->nullable()->after('telefono_celular');
            
            // Agregar CI (Cédula de Identidad)
            $table->string('ci', 20)->nullable()->unique()->after('telefono_fijo');
            
            // Agregar IDdepartamento (FK)
            $table->unsignedBigInteger('idDepartamento')->nullable()->after('institucion');
        });

        // ==============================
        // MODIFICAR TABLA DEPARTAMENTO
        // ==============================
        Schema::table('DEPARTAMENTO', function (Blueprint $table) {
            // Agregar Persona Encargada (FK a PERSONA)
            $table->unsignedBigInteger('idPersonaEncargada')->nullable()->after('nombre');
        });

        // ==============================
        // AGREGAR FOREIGN KEYS
        // ==============================

        Schema::table('PERSONA', function (Blueprint $table) {
            $table->foreign('idDepartamento')
                  ->references('idDepartamento')
                  ->on('DEPARTAMENTO')
                  ->onDelete('set null');
        });

        Schema::table('DEPARTAMENTO', function (Blueprint $table) {
            $table->foreign('idPersonaEncargada')
                  ->references('idPersona')
                  ->on('PERSONA')
                  ->onDelete('set null');
 $table->boolean('activo')
          ->default(true)
          ->after('idPersonaEncargada');
        });
       


        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();

        // Eliminar foreign keys
        Schema::table('PERSONA', function (Blueprint $table) {
            $table->dropForeignKey(['idDepartamento']);
            $table->dropColumn(['idDepartamento', 'ci', 'telefono']);
        });

        Schema::table('DEPARTAMENTO', function (Blueprint $table) {
            $table->dropForeignKey(['idPersonaEncargada']);
            $table->dropColumn('idPersonaEncargada');
        });

        Schema::enableForeignKeyConstraints();
    }
}
