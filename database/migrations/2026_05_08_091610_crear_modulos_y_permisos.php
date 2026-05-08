<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrearModulosYPermisos extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        // ==============================
        // TABLA MÓDULOS
        // ==============================
        Schema::create('MODULO', function (Blueprint $table) {
            $table->id('idModulo');
            $table->string('nombre', 100)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->boolean('activo')->default(true);
        });

        // ==============================
        // TABLA ROL_MODULO (Relación)
        // ==============================
        Schema::create('ROL_MODULO', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idRol');
            $table->unsignedBigInteger('idModulo');
            $table->boolean('activo')->default(true);
            
            $table->unique(['idRol', 'idModulo']);
        });

        // ==============================
        // AGREGAR FOREIGN KEYS
        // ==============================
        Schema::table('ROL_MODULO', function (Blueprint $table) {
            $table->foreign('idRol')
                  ->references('idRol')
                  ->on('ROL')
                  ->onDelete('cascade');
            
            $table->foreign('idModulo')
                  ->references('idModulo')
                  ->on('MODULO')
                  ->onDelete('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('ROL_MODULO');
        Schema::dropIfExists('MODULO');

        Schema::enableForeignKeyConstraints();
    }
}
