<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NivelUrgenciaSeeder extends Seeder
{
    public function run()
    {
        DB::table('NIVEL_URGENCIA')->insert([
            ['nombre' => 'Baja'],
            ['nombre' => 'Media'],
            ['nombre' => 'Alta'],
            ['nombre' => 'Urgente'],
        ]);
    }
}
