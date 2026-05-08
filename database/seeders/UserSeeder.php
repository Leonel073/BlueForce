<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ROL')->insert([
            ['nombre' => 'Administrador'], 
            ['nombre' => 'Usuario'],       
        ]);

        $idPersonaAdmin = DB::table('PERSONA')->insertGetId([
            'nombre' => 'Administrador del Sistema',
            'correo' => 'admin@blueforce.com',
            'telefono_celular' => '591-2-7654321',
            'telefono_fijo' => '591-2-2654321',
            'tipo' => 'INTERNO',
            'idDepartamento' => 1,
            'activo' => true,
        ]);

        $idPersonaJonathan = DB::table('PERSONA')->insertGetId([
            'nombre' => 'Jonathan Perez',
            'correo' => 'jonathan@blueforce.com',
            'telefono_celular' => '591-2-7654322',
            'telefono_fijo' => null,
            'tipo' => 'INTERNO',
            'idDepartamento' => 2,
            'activo' => true,
        ]);

        $idPersonaJorge = DB::table('PERSONA')->insertGetId([
            'nombre' => 'Ludwin Martinez',
            'correo' => 'ludwin33@gmail.com',
            'telefono_celular' => '591-2-7654323',
            'telefono_fijo' => null,
            'tipo' => 'INTERNO',
            'idDepartamento' => 3,
            'activo' => true,
        ]);

        User::create([
            'name' => 'Administrador',
            'email' => 'admin@blueforce.com',
            'password' => Hash::make('Admin.2026*'),
            'idPersona' => $idPersonaAdmin,
            'idRol' => 1,
        ]);

        User::create([
            'name' => 'Jonathan Perez',
            'email' => 'jonathan@gmail.com',
            'password' => Hash::make('Jona.2026#'),
            'idPersona' => $idPersonaJonathan,
            'idRol' => 2,
        ]);

        User::create([
            'name' => 'Ludwin Martinez',
            'email' => 'ludwin33@gmail.com',
            'password' => Hash::make('Sistema.2026*'),
            'idPersona' => $idPersonaJorge,
            'idRol' => 2,
        ]);
    }
}