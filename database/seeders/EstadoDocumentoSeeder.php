<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoDocumentoSeeder extends Seeder
{
    public function run()
    {
    

        DB::table('ESTADO_DOCUMENTO')->insert([
            ['nombre' => 'Pendiente'],
            ['nombre' => 'Recibido'],
            ['nombre' => 'Atendido'],
            ['nombre' => 'Archivado'],
        ]);
    }
}
