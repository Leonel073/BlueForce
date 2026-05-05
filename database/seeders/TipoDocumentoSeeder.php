<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class TipoDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('TIPO_DOCUMENTO')->insert([
            ['nombre' => 'Carta'],
            ['nombre' => 'Oficio'],
            ['nombre' => 'Memorándum'],
            ['nombre' => 'Informe'],
        ]);
    }
}
