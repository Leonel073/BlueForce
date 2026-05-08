<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonaSeeder extends Seeder
{
    public function run()
    {
        // Personas para cada departamento (3 por departamento = 18 personas)
        // Todos los CIs son únicos
        $personas = [
            // Ventanilla de Recepción (1) - 3 personas
            [
                'nombre' => 'María López García',
                'correo' => 'maria.lopez@epab.bo',
                'telefono_celular' => '591-2-7123456',
                'telefono_fijo' => '591-2-2123456',
                'ci' => '5678901234',
                'cargo' => 'Recepcionista',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 1,
                'activo' => true,
            ],
            [
                'nombre' => 'Carlos Mendez Rodriguez',
                'correo' => 'carlos.mendez@epab.bo',
                'telefono_celular' => '591-2-7234567',
                'telefono_fijo' => '591-2-2234567',
                'ci' => '6789012345',
                'cargo' => 'Encargado de Recepción',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 1,
                'activo' => true,
            ],
            [
                'nombre' => 'Verónica Torrez Mamani',
                'correo' => 'veronica.torrez@epab.bo',
                'telefono_celular' => '591-2-7345678',
                'telefono_fijo' => null,
                'ci' => '7123456789',
                'cargo' => 'Auxiliar de Recepción',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 1,
                'activo' => true,
            ],
            
            // Dirección General (2) - 3 personas
            [
                'nombre' => 'Dr. Juan Pérez Fernández',
                'correo' => 'juan.perez@epab.bo',
                'telefono_celular' => '591-2-7456789',
                'telefono_fijo' => '591-2-2345678',
                'ci' => '7890123456',
                'cargo' => 'Director General',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 2,
                'activo' => true,
            ],
            [
                'nombre' => 'Rosa Condori Quispe',
                'correo' => 'rosa.condori@epab.bo',
                'telefono_celular' => '591-2-7567890',
                'telefono_fijo' => '591-2-2456789',
                'ci' => '8901234567',
                'cargo' => 'Asistente de Dirección',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 2,
                'activo' => true,
            ],
            [
                'nombre' => 'Javier Alcázar Flores',
                'correo' => 'javier.alcazar@epab.bo',
                'telefono_celular' => '591-2-7678901',
                'telefono_fijo' => null,
                'ci' => '8234567890',
                'cargo' => 'Asesor Estratégico',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 2,
                'activo' => true,
            ],
            
            // Secretaría (3) - 3 personas
            [
                'nombre' => 'Luz María Vargas Torrico',
                'correo' => 'luz.vargas@epab.bo',
                'telefono_celular' => '591-2-7789012',
                'telefono_fijo' => '591-2-2567890',
                'ci' => '9012345678',
                'cargo' => 'Secretaria General',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 3,
                'activo' => true,
            ],
            [
                'nombre' => 'Patricia Reyes López',
                'correo' => 'patricia.reyes@epab.bo',
                'telefono_celular' => '591-2-7890123',
                'telefono_fijo' => '591-2-2678901',
                'ci' => '4567890123',
                'cargo' => 'Asistente Administrativo',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 3,
                'activo' => true,
            ],
            [
                'nombre' => 'Samuel González Yucra',
                'correo' => 'samuel.gonzalez@epab.bo',
                'telefono_celular' => '591-2-7901234',
                'telefono_fijo' => null,
                'ci' => '9234567890',
                'cargo' => 'Secretario Ejecutivo',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 3,
                'activo' => true,
            ],
            
            // Departamento Administrativo (4) - 3 personas
            [
                'nombre' => 'Luis Alberto Soto Morales',
                'correo' => 'luis.soto@epab.bo',
                'telefono_celular' => '591-2-7012345',
                'telefono_fijo' => '591-2-2789012',
                'ci' => '5432109876',
                'cargo' => 'Jefe Administrativo',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 4,
                'activo' => true,
            ],
            [
                'nombre' => 'Daniela Flores Gutierrez',
                'correo' => 'daniela.flores@epab.bo',
                'telefono_celular' => '591-2-7123457',
                'telefono_fijo' => '591-2-2890123',
                'ci' => '6543210987',
                'cargo' => 'Asistente Administrativo',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 4,
                'activo' => true,
            ],
            [
                'nombre' => 'Marcela Gutiérrez Aguilar',
                'correo' => 'marcela.gutierrez@epab.bo',
                'telefono_celular' => '591-2-7234568',
                'telefono_fijo' => null,
                'ci' => '3456789012',
                'cargo' => 'Coordinadora Administrativa',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 4,
                'activo' => true,
            ],
            
            // Departamento Financiero (5) - 3 personas
            [
                'nombre' => 'Roberto Chambi Alanoca',
                'correo' => 'roberto.chambi@epab.bo',
                'telefono_celular' => '591-2-7345679',
                'telefono_fijo' => '591-2-2901234',
                'ci' => '7654321098',
                'cargo' => 'Contador',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 5,
                'activo' => true,
            ],
            [
                'nombre' => 'Marcela Choquetilda Murillo',
                'correo' => 'marcela.choquetilda@epab.bo',
                'telefono_celular' => '591-2-7456780',
                'telefono_fijo' => '591-2-3012345',
                'ci' => '8765432109',
                'cargo' => 'Tesorera',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 5,
                'activo' => true,
            ],
            [
                'nombre' => 'Óscar Lázaro Paz',
                'correo' => 'oscar.lazaro@epab.bo',
                'telefono_celular' => '591-2-7567891',
                'telefono_fijo' => null,
                'ci' => '9345678901',
                'cargo' => 'Analista Financiero',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 5,
                'activo' => true,
            ],
            
            // Recursos Humanos (6) - 3 personas
            [
                'nombre' => 'Valentina Rojas Sánchez',
                'correo' => 'valentina.rojas@epab.bo',
                'telefono_celular' => '591-2-7678902',
                'telefono_fijo' => '591-2-3123456',
                'ci' => '9876543210',
                'cargo' => 'Jefa de RRHH',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 6,
                'activo' => true,
            ],
            [
                'nombre' => 'Fernando Quispe Mamani',
                'correo' => 'fernando.quispe@epab.bo',
                'telefono_celular' => '591-2-7789013',
                'telefono_fijo' => '591-2-3234567',
                'ci' => '1234567890',
                'cargo' => 'Especialista en RRHH',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 6,
                'activo' => true,
            ],
            [
                'nombre' => 'Sofía Peña Vargas',
                'correo' => 'sofia.pena@epab.bo',
                'telefono_celular' => '591-2-7890124',
                'telefono_fijo' => null,
                'ci' => '2345678901',
                'cargo' => 'Asistente de RRHH',
                'institucion' => 'EPAB',
                'tipo' => 'INTERNO',
                'idDepartamento' => 6,
                'activo' => true,
            ],
        ];

        DB::table('PERSONA')->insert($personas);
    }
}


