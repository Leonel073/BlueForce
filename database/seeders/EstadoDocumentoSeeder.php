<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoDocumentoSeeder extends Seeder
{
    public function run()
    {
        // Limpiar estados anteriores para evitar duplicados
        DB::table('ESTADO_DOCUMENTO')->truncate();

        DB::table('ESTADO_DOCUMENTO')->insert([
            ['nombre' => 'Pendiente'],
            ['nombre' => 'Recibido'],
            ['nombre' => 'Atendido'],
            ['nombre' => 'Archivado'],
        ]);
    }
}
