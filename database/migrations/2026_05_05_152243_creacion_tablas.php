<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreacionTablas extends Migration
{
    public function up()
    {
        // DESACTIVAR FK TEMPORALMENTE
        Schema::disableForeignKeyConstraints();

        // ==============================
        // CATÁLOGOS
        // ==============================

        Schema::create('ROL', function (Blueprint $table) {
            $table->id('idRol');
            $table->string('nombre', 100);
        });

        Schema::create('ESTADO_USUARIO', function (Blueprint $table) {
            $table->id('idEstadoUsuario');
            $table->string('nombre', 100);
        });

        Schema::create('DEPARTAMENTO', function (Blueprint $table) {
            $table->id('idDepartamento');
            $table->string('nombre', 150);
        });

        Schema::create('ESTADO_DOCUMENTO', function (Blueprint $table) {
            $table->id('idEstado');
            $table->string('nombre', 100);
        });

        Schema::create('NIVEL_URGENCIA', function (Blueprint $table) {
            $table->id('idUrgencia');
            $table->string('nombre', 100);
        });

        Schema::create('TIPO_DOCUMENTO', function (Blueprint $table) {
            $table->id('idTipoDocumento');
            $table->string('nombre', 150);
        });

        // ==============================
        // PERSONA
        // ==============================

        Schema::create('PERSONA', function (Blueprint $table) {
            $table->id('idPersona');
            $table->string('nombre', 200);
            $table->string('correo', 150)->nullable();
            $table->string('institucion', 200)->nullable();
            $table->enum('tipo', ['INTERNO','EXTERNO']);
            $table->boolean('activo')->default(true);
        });

        // ==============================
        // CORRESPONDENCIA
        // ==============================

        Schema::create('CORRESPONDENCIA', function (Blueprint $table) {
            $table->id('idDocumento');
            $table->string('cite',100);
            $table->text('asunto');
            $table->dateTime('fecha')->useCurrent();

            $table->unsignedBigInteger('idTipoDocumento')->nullable();
            $table->unsignedBigInteger('idEstado')->nullable();
            $table->unsignedBigInteger('idUrgencia')->nullable();
            $table->unsignedBigInteger('idUsuario')->nullable();
            $table->unsignedBigInteger('idRemitente')->nullable();

            $table->boolean('activo')->default(true);
        });

        // ==============================
        // DESTINATARIOS
        // ==============================

        Schema::create('CORRESPONDENCIA_DESTINATARIO', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idDocumento');
            $table->unsignedBigInteger('idPersona');
            $table->boolean('activo')->default(true);
        });

        // ==============================
        // AUDITORIA
        // ==============================

        Schema::create('AUDITORIA', function (Blueprint $table) {
            $table->id('idLog');
            $table->unsignedBigInteger('idUsuario')->nullable();
            $table->text('accion');
            $table->dateTime('fecha')->useCurrent();
        });

        // ==============================
        // DERIVACION
        // ==============================

        Schema::create('DERIVACION', function (Blueprint $table) {
            $table->id('idDerivacion');
            $table->unsignedBigInteger('idDocumento');
            $table->integer('orden');

            $table->unsignedBigInteger('idDepartamentoOrigen');
            $table->unsignedBigInteger('idDepartamentoDestino');

            $table->unsignedBigInteger('idUsuarioAsignado')->nullable();
            $table->text('instruccion')->nullable();

            $table->dateTime('fechaEnvio')->useCurrent();
            $table->dateTime('fechaRecepcion')->nullable();
            $table->boolean('activo')->default(true);
        });

        // ==============================
        // SEGUIMIENTO
        // ==============================

        Schema::create('SEGUIMIENTO', function (Blueprint $table) {
            $table->id('idSeguimiento');
            $table->unsignedBigInteger('idDocumento');
            $table->dateTime('fecha')->useCurrent();
            $table->string('ubicacion',255);
            $table->unsignedBigInteger('idEstado')->nullable();
            $table->boolean('activo')->default(true);
        });

        // ==============================
        // CODIGO RUTA
        // ==============================

        Schema::create('CODIGO_RUTA', function (Blueprint $table) {
            $table->id('idCodigo');
            $table->unsignedBigInteger('idDocumento');
            $table->string('codigo',100)->unique();
            $table->dateTime('fechaGeneracion')->useCurrent();
            $table->boolean('activo')->default(true);
        });

        // ==============================
        // FOREIGN KEYS (AL FINAL)
        // ==============================

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('idPersona')->references('idPersona')->on('PERSONA');
            $table->foreign('idRol')->references('idRol')->on('ROL');
            $table->foreign('idEstadoUsuario')->references('idEstadoUsuario')->on('ESTADO_USUARIO');
        });

        Schema::table('CORRESPONDENCIA', function (Blueprint $table) {
            $table->foreign('idTipoDocumento')->references('idTipoDocumento')->on('TIPO_DOCUMENTO');
            $table->foreign('idEstado')->references('idEstado')->on('ESTADO_DOCUMENTO');
            $table->foreign('idUrgencia')->references('idUrgencia')->on('NIVEL_URGENCIA');
            $table->foreign('idUsuario')->references('id')->on('users');
            $table->foreign('idRemitente')->references('idPersona')->on('PERSONA');
        });

        Schema::table('CORRESPONDENCIA_DESTINATARIO', function (Blueprint $table) {
            $table->foreign('idDocumento')->references('idDocumento')->on('CORRESPONDENCIA');
            $table->foreign('idPersona')->references('idPersona')->on('PERSONA');
        });

        Schema::table('AUDITORIA', function (Blueprint $table) {
            $table->foreign('idUsuario')->references('id')->on('users');
        });

        Schema::table('DERIVACION', function (Blueprint $table) {
            $table->foreign('idDocumento')->references('idDocumento')->on('CORRESPONDENCIA');
            $table->foreign('idDepartamentoOrigen')->references('idDepartamento')->on('DEPARTAMENTO');
            $table->foreign('idDepartamentoDestino')->references('idDepartamento')->on('DEPARTAMENTO');
            $table->foreign('idUsuarioAsignado')->references('id')->on('users');
        });

        Schema::table('SEGUIMIENTO', function (Blueprint $table) {
            $table->foreign('idDocumento')->references('idDocumento')->on('CORRESPONDENCIA');
            $table->foreign('idEstado')->references('idEstado')->on('ESTADO_DOCUMENTO');
        });

        Schema::table('CODIGO_RUTA', function (Blueprint $table) {
            $table->foreign('idDocumento')->references('idDocumento')->on('CORRESPONDENCIA');
        });

        // ACTIVAR FK
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('CODIGO_RUTA');
        Schema::dropIfExists('SEGUIMIENTO');
        Schema::dropIfExists('DERIVACION');
        Schema::dropIfExists('AUDITORIA');
        Schema::dropIfExists('CORRESPONDENCIA_DESTINATARIO');
        Schema::dropIfExists('CORRESPONDENCIA');
        
        // Se elimina la referencia a USUARIO

        Schema::dropIfExists('PERSONA');
        Schema::dropIfExists('TIPO_DOCUMENTO');
        Schema::dropIfExists('NIVEL_URGENCIA');
        Schema::dropIfExists('ESTADO_DOCUMENTO');
        Schema::dropIfExists('DEPARTAMENTO');
        Schema::dropIfExists('ESTADO_USUARIO');
        Schema::dropIfExists('ROL');

        Schema::enableForeignKeyConstraints();
    }
}