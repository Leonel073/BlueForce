<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NivelUrgenciaSeeder extends Seeder
{
    public function run()
    {
        DB::table('NIVEL_URGENCIA')->insert([
            ['nombre' => 'Alta'],
            ['nombre' => 'Medio'],
            ['nombre' => 'Bajo'],
        ]);
    }
}
