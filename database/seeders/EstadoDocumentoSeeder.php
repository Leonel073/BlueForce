<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ESTADO_DOCUMENTO')->insert([
            ['nombre' => 'Pendiente'],
            ['nombre' => 'En proceso'],
            ['nombre' => 'Finalizado'],
        ]);
    }
}
