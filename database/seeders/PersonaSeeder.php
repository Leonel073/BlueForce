<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PersonaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('PERSONA')->insert([
            [
                'nombre' => 'Juan Perez',
                'correo' => 'juan@test.com',
                'cargo' => 'Administrador',
                'institucion' => 'Estado Mayor',
                'tipo' => 'INTERNO',
                'activo' => true
            ],
            [
                'nombre' => 'Maria Lopez',
                'correo' => 'maria@test.com',
                'cargo' => 'Usuario',
                'institucion' => 'Policlinica',
                'tipo' => 'EXTERNO',
                'activo' => true
            ]
        ]);
    }
}
