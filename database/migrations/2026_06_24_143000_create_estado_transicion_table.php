<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstadoTransicionTable extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('ESTADO_TRANSICION', function (Blueprint $table) {
            $table->id('idTransicion');
            
            // Documento relacionado
            $table->unsignedBigInteger('idDocumento');
            
            // Usuario que realiza la acción
            $table->unsignedBigInteger('idUsuario')->nullable();
            
            // Estados anterior y nuevo
            $table->unsignedBigInteger('idEstadoAnterior')->nullable();
            $table->unsignedBigInteger('idEstadoNuevo');
            
            // Tipo de acción
            $table->enum('accion', [
                'CREAR',
                'DERIVAR',
                'RECIBIR',
                'ATENDER',
                'ARCHIVAR'
            ]);
            
            // Información adicional
            $table->text('observacion')->nullable();
            
            // Fecha y hora de la transición
            $table->dateTime('fecha')->useCurrent();
            
            // Soft delete
            $table->boolean('activo')->default(true);
            
            // Índices
            $table->index('idDocumento');
            $table->index('idUsuario');
            $table->index('fecha');
            $table->index('accion');
        });

        // Foreign Keys
        Schema::table('ESTADO_TRANSICION', function (Blueprint $table) {
            $table->foreign('idDocumento')
                  ->references('idDocumento')
                  ->on('CORRESPONDENCIA')
                  ->onDelete('cascade');
            
            $table->foreign('idUsuario')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            $table->foreign('idEstadoAnterior')
                  ->references('idEstado')
                  ->on('ESTADO_DOCUMENTO')
                  ->onDelete('set null');
            
            $table->foreign('idEstadoNuevo')
                  ->references('idEstado')
                  ->on('ESTADO_DOCUMENTO')
                  ->onDelete('restrict');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('ESTADO_TRANSICION');
        Schema::enableForeignKeyConstraints();
    }
}
