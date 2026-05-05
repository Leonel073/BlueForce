<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class NivelUrgenciaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('NIVEL_URGENCIA')->insert([
            ['nombre' => 'Bajo'],
            ['nombre' => 'Medio'],
            ['nombre' => 'Alto'],
            ['nombre' => 'Urgente'],
        ]);
    }
}
