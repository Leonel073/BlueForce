<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::updateOrCreate(
        ['correo' => 'admin@test.com'],
        [
        'contrasena' => Hash::make('123456'),
        'idPersona' => 1,
        'idRol' => 1,
        'idEstadoUsuario' => 1,
        'activo' => true,
    ]
);
    }
}