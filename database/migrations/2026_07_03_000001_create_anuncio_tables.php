<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ANUNCIO', function (Blueprint $table) {
            $table->id('idAnuncio');
            $table->string('titulo', 255);
            $table->text('asunto');
            $table->string('archivo_pdf', 255)->nullable()->comment('Nombre original del PDF');
            $table->string('ruta_pdf', 500)->nullable()->comment('Ruta en disco privado');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('tamano_archivo')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('idUsuarioCreador');
            $table->timestamp('fechaCreacion')->useCurrent();

            $table->foreign('idUsuarioCreador')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete();
        });

        Schema::create('ANUNCIO_VISTO', function (Blueprint $table) {
            $table->id('idAnuncioVisto');
            $table->unsignedBigInteger('idAnuncio');
            $table->unsignedBigInteger('idUsuario');
            $table->timestamp('fechaVisto')->useCurrent();

            $table->foreign('idAnuncio')
                  ->references('idAnuncio')
                  ->on('ANUNCIO')
                  ->cascadeOnDelete();

            $table->foreign('idUsuario')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnDelete();

            $table->unique(['idAnuncio', 'idUsuario']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ANUNCIO_VISTO');
        Schema::dropIfExists('ANUNCIO');
    }
};
