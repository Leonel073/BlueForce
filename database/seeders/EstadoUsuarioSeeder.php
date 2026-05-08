<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoUsuarioSeeder extends Seeder
{
    public function run()
    {
        DB::table('ESTADO_USUARIO')->insert([
            ['nombre' => 'Activo'],
            ['nombre' => 'Inactivo'],
            ['nombre' => 'Bloqueado'],
        ]);
    }
}
