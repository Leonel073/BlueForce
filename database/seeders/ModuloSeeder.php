<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuloSeeder extends Seeder
{
    public function run()
    {
        // Crear módulos
        DB::table('MODULO')->insert([
            ['nombre' => 'documentos', 'descripcion' => 'Gestión de documentos y correspondencia'],
            ['nombre' => 'departamento', 'descripcion' => 'Gestión de departamentos y responsables'],
            ['nombre' => 'usuarios', 'descripcion' => 'Gestión de usuarios y roles'],
            ['nombre' => 'reportes', 'descripcion' => 'Reportes y estadísticas'],
        ]);

        // Asignar módulos a roles
        // Rol: Administrador (1)
        DB::table('ROL_MODULO')->insert([
            ['idRol' => 1, 'idModulo' => 1], // documentos
            ['idRol' => 1, 'idModulo' => 2], // departamento ← AGREGADO
            ['idRol' => 1, 'idModulo' => 3], // usuarios
            ['idRol' => 1, 'idModulo' => 4], // reportes
        ]);

        // Rol: Usuario (2)
        DB::table('ROL_MODULO')->insert([
            ['idRol' => 2, 'idModulo' => 1], // documentos
            ['idRol' => 2, 'idModulo' => 4], // reportes
        ]);

        // Rol: Encargado Departamento (3)
        DB::table('ROL_MODULO')->insert([
            ['idRol' => 3, 'idModulo' => 1], // documentos
            ['idRol' => 3, 'idModulo' => 4], // reportes
        ]);
    }
}
