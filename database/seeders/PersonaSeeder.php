<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonaSeeder extends Seeder
{
    public function run()
    {
        /*
        |--------------------------------------------------------------------------
        | MAPEO DE CARGOS A SUS IDs
        |--------------------------------------------------------------------------
        | Se obtienen los IDs de la tabla CARGO basados en sus nombres
        |--------------------------------------------------------------------------
        */

        $cargos = DB::table('CARGO')
            ->pluck('idCargo', 'nombre')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | PERSONAS POR DEPARTAMENTO
        |--------------------------------------------------------------------------
        | Cada persona está asignada a un departamento y tiene un cargo
        | Total: 18 personas (3 por departamento)
        |--------------------------------------------------------------------------
        */

        $personas = [
            
            /*
            |------
            | VENTANILLA DE RECEPCIÓN (Departamento 1)
            |------
            */
            
            [
                'nombre' => 'María López García',
                'correo' => 'maria.lopez@epab.bo',
                'telefono_celular' => '591-2-7123456',
                'telefono_fijo' => '591-2-2123456',
                'ci' => '7845123',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 1,
                'idCargo' => $cargos['Recepcionista'] ?? null,
                'activo' => true,
            ],
            
            [
                'nombre' => 'Carlos Mendez Rodriguez',
                'correo' => 'carlos.mendez@epab.bo',
                'telefono_celular' => '591-2-7234567',
                'telefono_fijo' => '591-2-2234567',
                'ci' => '6523412',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 1,
                'idCargo' => $cargos['Encargado de Recepción'] ?? null,
                'activo' => true,
            ],
            
            [
                'nombre' => 'Verónica Torrez Mamani',
                'correo' => 'veronica.torrez@epab.bo',
                'telefono_celular' => '591-2-7345678',
                'telefono_fijo' => null,
                'ci' => '9134578',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 1,
                'idCargo' => $cargos['Auxiliar de Recepción'] ?? null,
                'activo' => true,
            ],
            
            /*
            |------
            | DIRECCIÓN GENERAL (Departamento 2)
            |------
            */
            
            [
                'nombre' => 'Dr. Juan Pérez Fernández',
                'correo' => 'juan.perez@epab.bo',
                'telefono_celular' => '591-2-7456789',
                'telefono_fijo' => '591-2-2345678',
                'ci' => '4875213',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 2,
                'idCargo' => $cargos['Director General'] ?? null,
                'activo' => true,
            ],
            
            [
                'nombre' => 'Rosa Condori Quispe',
                'correo' => 'rosa.condori@epab.bo',
                'telefono_celular' => '591-2-7567890',
                'telefono_fijo' => '591-2-2456789',
                'ci' => '7458963',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 2,
                'idCargo' => $cargos['Asistente de Dirección'] ?? null,
                'activo' => true,
            ],
            
            [
                'nombre' => 'Javier Alcázar Flores',
                'correo' => 'javier.alcazar@epab.bo',
                'telefono_celular' => '591-2-7678901',
                'telefono_fijo' => null,
                'ci' => '5689741',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 2,
                'idCargo' => $cargos['Asesor Estratégico'] ?? null,
                'activo' => true,
            ],
            
            /*
            |------
            | SECRETARÍA (Departamento 3)
            |------
            */
            
            [
                'nombre' => 'Luz María Vargas Torrico',
                'correo' => 'luz.vargas@epab.bo',
                'telefono_celular' => '591-2-7789012',
                'telefono_fijo' => '591-2-2567890',
                'ci' => '3214789',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 3,
                'idCargo' => $cargos['Secretaria General'] ?? null,
                'activo' => true,
            ],
            
            [
                'nombre' => 'Patricia Reyes López',
                'correo' => 'patricia.reyes@epab.bo',
                'telefono_celular' => '591-2-7890123',
                'telefono_fijo' => '591-2-2678901',
                'ci' => '8521476',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 3,
                'idCargo' => $cargos['Auxiliar Administrativo'] ?? null,
                'activo' => true,
            ],
            
            [
                'nombre' => 'Samuel González Yucra',
                'correo' => 'samuel.gonzalez@epab.bo',
                'telefono_celular' => '591-2-7901234',
                'telefono_fijo' => null,
                'ci' => '9632587',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 3,
                'idCargo' => $cargos['Asistente Administrativo'] ?? null,
                'activo' => true,
            ],
            
            /*
            |------
            | DEPARTAMENTO ADMINISTRATIVO (Departamento 4)
            |------
            */
            
            [
                'nombre' => 'Luis Alberto Soto Morales',
                'correo' => 'luis.soto@epab.bo',
                'telefono_celular' => '591-2-7012345',
                'telefono_fijo' => '591-2-2789012',
                'ci' => '7412369',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 4,
                'idCargo' => $cargos['Especialista Administrativo'] ?? null,
                'activo' => true,
            ],
            
            [
                'nombre' => 'Daniela Flores Gutierrez',
                'correo' => 'daniela.flores@epab.bo',
                'telefono_celular' => '591-2-7123457',
                'telefono_fijo' => '591-2-2890123',
                'ci' => '6543210',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 4,
                'idCargo' => $cargos['Auxiliar Administrativo'] ?? null,
                'activo' => true,
            ],
            
            [
                'nombre' => 'Marcela Gutiérrez Aguilar',
                'correo' => 'marcela.gutierrez@epab.bo',
                'telefono_celular' => '591-2-7234568',
                'telefono_fijo' => null,
                'ci' => '3456789',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 4,
                'idCargo' => $cargos['Especialista Administrativo'] ?? null,
                'activo' => true,
            ],
            
            /*
            |------
            | DEPARTAMENTO FINANCIERO (Departamento 5)
            |------
            */
            
            [
                'nombre' => 'Roberto Chambi Alanoca',
                'correo' => 'roberto.chambi@epab.bo',
                'telefono_celular' => '591-2-7345679',
                'telefono_fijo' => '591-2-2901234',
                'ci' => '7654321',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 5,
                'idCargo' => $cargos['Especialista Financiero'] ?? null,
                'activo' => true,
            ],
            
            [
                'nombre' => 'Marcela Choquetilda Murillo',
                'correo' => 'marcela.choquetilda@epab.bo',
                'telefono_celular' => '591-2-7456780',
                'telefono_fijo' => '591-2-3012345',
                'ci' => '8765432',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 5,
                'idCargo' => $cargos['Especialista Financiero'] ?? null,
                'activo' => true,
            ],
            
            [
                'nombre' => 'Óscar Lázaro Paz',
                'correo' => 'oscar.lazaro@epab.bo',
                'telefono_celular' => '591-2-7567891',
                'telefono_fijo' => null,
                'ci' => '9345678',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 5,
                'idCargo' => $cargos['Especialista Administrativo'] ?? null,
                'activo' => true,
            ],
            
            /*
            |------
            | RECURSOS HUMANOS (Departamento 6)
            |------
            */
            
            [
                'nombre' => 'Valentina Rojas Sánchez',
                'correo' => 'valentina.rojas@epab.bo',
                'telefono_celular' => '591-2-7678902',
                'telefono_fijo' => '591-2-3123456',
                'ci' => '9876543',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 6,
                'idCargo' => $cargos['Especialista en RRHH'] ?? null,
                'activo' => true,
            ],
            
            [
                'nombre' => 'Fernando Quispe Mamani',
                'correo' => 'fernando.quispe@epab.bo',
                'telefono_celular' => '591-2-7789013',
                'telefono_fijo' => '591-2-3234567',
                'ci' => '1234567',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 6,
                'idCargo' => $cargos['Especialista en RRHH'] ?? null,
                'activo' => true,
            ],
            
            [
                'nombre' => 'Sofía Peña Vargas',
                'correo' => 'sofia.pena@epab.bo',
                'telefono_celular' => '591-2-7890124',
                'telefono_fijo' => null,
                'ci' => '2345678',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 6,
                'idCargo' => $cargos['Auxiliar Administrativo'] ?? null,
                'activo' => true,
            ],
        ];

        DB::table('PERSONA')->insert($personas);
    }
}

