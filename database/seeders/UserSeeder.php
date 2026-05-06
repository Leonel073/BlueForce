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
            'tipo' => 'INTERNO',
        ]);

        $idPersonaJonathan = DB::table('PERSONA')->insertGetId([
            'nombre' => 'Jonathan',
            'correo' => 'jonathan@blueforce.com',
            'tipo' => 'INTERNO',
        ]);

        $idPersonaJorge = DB::table('PERSONA')->insertGetId([
            'nombre' => 'Jorge',
            'correo' => 'jorge@blueforce.com',
            'tipo' => 'INTERNO',
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