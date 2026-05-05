<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    public function run()
    {
        DB::table('DEPARTAMENTO')->insert([
            ['nombre' => 'Ventanilla de Recepción'],
            ['nombre' => 'Dirección General'],
            ['nombre' => 'Secretaría'],
            ['nombre' => 'Departamento Administrativo'],
            ['nombre' => 'Departamento Financiero'],
            ['nombre' => 'Recursos Humanos'],
        ]);
    }
}
