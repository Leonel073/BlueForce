<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            
            /*
            |------
            | CATÁLOGOS BASE (SIN DEPENDENCIAS)
            |------
            */
            
            RolSeeder::class,
            DepartamentoSeeder::class,
            TipoDocumentoSeeder::class,
            EstadoDocumentoSeeder::class,
            NivelUrgenciaSeeder::class,
            
            /*
            |------
            | CATÁLOGO DE CARGOS (DEBE IR ANTES DE PERSONAS)
            |------
            */
            
            CargoSeeder::class,
            
            /*
            |------
            | PERSONAS (DEPENDE DE CARGOS)
            |------
            */
            
            PersonaSeeder::class,
            
            /*
            |------
            | USUARIOS FINALES
            |------
            */
            
            UserSeeder::class,
        ]);
    }
}
