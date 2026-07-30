<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentoSeeder extends Seeder
{
    public function run()
    {
        DB::table('TIPO_DOCUMENTO')->insert([
            ['nombre' => 'Memorándum'],
            ['nombre' => 'Informe'],
            ['nombre' => 'Solicitud'],
            ['nombre' => 'Carta'],
            ['nombre' => 'Oficio'],
            ['nombre' => 'Correo Electronicos'],
            ['nombre' => 'FAX'],
            ['nombre' => 'Notas de Servicio'],
            ['nombre' => 'Certificado'],
        ]);
    }
}
