<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EliminarEstadoUsuario extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        // ==============================
        // ELIMINAR REFERENCIA EN TABLA CREACION_TABLAS
        // ==============================
        Schema::table('users', function (Blueprint $table) {
            // Eliminar la FK si existe
            if (Schema::hasColumn('users', 'idEstadoUsuario')) {
                // Verificar si existe la constrainta antes de eliminarla
                try {
                    $table->dropForeign(['idEstadoUsuario']);
                } catch (\Exception $e) {
                    // FK no existe, continuar
                }
                $table->dropColumn('idEstadoUsuario');
            }
        });

        // ==============================
        // ELIMINAR TABLA ESTADO_USUARIO
        // ==============================
        if (Schema::hasTable('ESTADO_USUARIO')) {
            Schema::drop('ESTADO_USUARIO');
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();

        // Recrear tabla ESTADO_USUARIO
        Schema::create('ESTADO_USUARIO', function (Blueprint $table) {
            $table->id('idEstadoUsuario');
            $table->string('nombre', 100);
        });

        // Agregar columna nuevamente
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('idEstadoUsuario')->nullable()->after('idRol');
            $table->foreign('idEstadoUsuario')
                  ->references('idEstadoUsuario')
                  ->on('ESTADO_USUARIO');
        });

        Schema::enableForeignKeyConstraints();
    }
}
