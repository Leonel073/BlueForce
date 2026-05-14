<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificacionesBaseDatos extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        /*
        |--------------------------------------------------------------------------
        | MODIFICAR TABLA PERSONA
        |--------------------------------------------------------------------------
        | Agregar campos de contacto, identificación y relaciones
        |--------------------------------------------------------------------------
        */

        Schema::table('PERSONA', function (Blueprint $table) {
            
            // Agregar teléfono celular (obligatorio)
            $table->string('telefono_celular', 20)
                  ->after('correo')
                  ->comment('Número de teléfono celular de la persona');
            
            // Agregar teléfono fijo (opcional)
            $table->string('telefono_fijo', 20)
                  ->nullable()
                  ->after('telefono_celular')
                  ->comment('Número de teléfono fijo de la persona');
            
            // Agregar CI (Cédula de Identidad)
            $table->string('ci', 20)
                  ->nullable()
                  ->unique()
                  ->after('telefono_fijo')
                  ->comment('Cédula de identidad de la persona');
            
            // Agregar IDdepartamento (FK)
            $table->unsignedBigInteger('idDepartamento')
                  ->nullable()
                  ->after('institucion')
                  ->comment('Referencia al departamento donde trabaja');
        });

        /*
        |--------------------------------------------------------------------------
        | MODIFICAR TABLA DEPARTAMENTO
        |--------------------------------------------------------------------------
        | Agregar campos de responsable y estado
        |--------------------------------------------------------------------------
        */

        Schema::table('DEPARTAMENTO', function (Blueprint $table) {
            
            // Agregar Persona Encargada (FK a PERSONA)
            $table->unsignedBigInteger('idPersonaEncargada')
                  ->nullable()
                  ->after('nombre')
                  ->comment('Persona encargada del departamento');
            
            // Agregar estado activo
            $table->boolean('activo')
                  ->default(true)
                  ->after('idPersonaEncargada')
                  ->comment('Indica si el departamento está activo');
        });

        /*
        |--------------------------------------------------------------------------
        | AGREGAR FOREIGN KEYS
        |--------------------------------------------------------------------------
        */

        Schema::table('PERSONA', function (Blueprint $table) {
            $table->foreign('idDepartamento')
                  ->references('idDepartamento')
                  ->on('DEPARTAMENTO')
                  ->onDelete('set null')
                  ->comment('FK: PERSONA.idDepartamento -> DEPARTAMENTO.idDepartamento');
        });

        Schema::table('DEPARTAMENTO', function (Blueprint $table) {
            $table->foreign('idPersonaEncargada')
                  ->references('idPersona')
                  ->on('PERSONA')
                  ->onDelete('set null')
                  ->comment('FK: DEPARTAMENTO.idPersonaEncargada -> PERSONA.idPersona');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();

        // Eliminar foreign keys
        Schema::table('PERSONA', function (Blueprint $table) {
            $table->dropForeignKey(['idDepartamento']);
            $table->dropColumn(['idDepartamento', 'ci', 'telefono_celular', 'telefono_fijo']);
        });

        Schema::table('DEPARTAMENTO', function (Blueprint $table) {
            $table->dropForeignKey(['idPersonaEncargada']);
            $table->dropColumn(['idPersonaEncargada', 'activo']);
        });

        Schema::enableForeignKeyConstraints();
    }
}
