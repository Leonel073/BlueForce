<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Catálogos base
            RolSeeder::class,
            DepartamentoSeeder::class,
            TipoDocumentoSeeder::class,
            EstadoDocumentoSeeder::class,
            NivelUrgenciaSeeder::class,
            
            // Módulos y permisos
          
            
            // Personas (con departamentos asignados)
            PersonaSeeder::class,
            
            // Usuarios
            UserSeeder::class,
        ]);
    }
}
