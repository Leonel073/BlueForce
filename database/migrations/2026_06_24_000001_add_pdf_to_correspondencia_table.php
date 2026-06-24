<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega columnas para almacenar archivos PDF adjuntos a documentos.
     * Todas las columnas son nullable para no afectar registros existentes.
     */
    public function up(): void
    {
        Schema::table('CORRESPONDENCIA', function (Blueprint $table) {
            $table->string('archivo_pdf', 255)
                  ->nullable()
                  ->after('activo')
                  ->comment('Nombre original del archivo PDF subido');

            $table->string('ruta_pdf', 500)
                  ->nullable()
                  ->after('archivo_pdf')
                  ->comment('Ruta de almacenamiento en disco privado (local)');

            $table->string('mime_type', 100)
                  ->nullable()
                  ->after('ruta_pdf')
                  ->comment('Tipo MIME verificado con finfo del archivo');

            $table->unsignedBigInteger('tamano_archivo')
                  ->nullable()
                  ->after('mime_type')
                  ->comment('Tamaño del archivo en bytes');

            $table->timestamp('fecha_subida')
                  ->nullable()
                  ->after('tamano_archivo')
                  ->comment('Fecha y hora en que se subió el archivo');

            $table->unsignedBigInteger('idUsuarioPdf')
                  ->nullable()
                  ->after('fecha_subida')
                  ->comment('Usuario que subió el PDF (auditoría)');

            $table->foreign('idUsuarioPdf')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('CORRESPONDENCIA', function (Blueprint $table) {
            $table->dropForeign(['idUsuarioPdf']);
            $table->dropColumn([
                'archivo_pdf',
                'ruta_pdf',
                'mime_type',
                'tamano_archivo',
                'fecha_subida',
                'idUsuarioPdf',
            ]);
        });
    }
};
