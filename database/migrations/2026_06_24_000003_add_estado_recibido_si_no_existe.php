<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Asegura que los estados oficiales existan en la tabla ESTADO_DOCUMENTO.
 * No trunca datos existentes para no romper FKs.
 * Solo inserta los estados que falten.
 */
return new class extends Migration
{
    public function up(): void
    {
        $estadosRequeridos = ['Pendiente', 'Recibido', 'Atendido', 'Archivado'];

        foreach ($estadosRequeridos as $nombre) {
            $existe = DB::table('ESTADO_DOCUMENTO')
                ->where('nombre', $nombre)
                ->exists();

            if (!$existe) {
                DB::table('ESTADO_DOCUMENTO')->insert(['nombre' => $nombre]);
            }
        }
    }

    public function down(): void
    {
        // No revertir: los estados son datos de catálogo
    }
};
