<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        DB::table('USUARIO')->insert([
            'correo' => 'admin@gmail.com',
            'contrasena' => Hash::make('123456'),
            'idPersona' => 1,
            'idRol' => 1, 
            'idEstadoUsuario' => 1, 
            'activo' => 1
        ]);
    }
}
