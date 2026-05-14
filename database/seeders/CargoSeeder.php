<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoSeeder extends Seeder
{
    public function run()
    {
        /*
        |--------------------------------------------------------------------------
        | CARGOS - ESCUELA DE POSTGRADO ARMADA BOLIVIANA
        |--------------------------------------------------------------------------
        | Estructura de cargos típicos para una institución militar académica
        |--------------------------------------------------------------------------
        */

        DB::table('CARGO')->insert([
            
            /*
            |------
            | NIVEL DIRECTIVO
            |------
            */
            
            [
                'nombre' => 'Director General',
                'descripcion' => 'Responsable de la dirección general de la escuela de postgrado',
                'nivel' => 'Directivo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Subdirector Académico',
                'descripcion' => 'Responsable de la supervisión académica y programas de estudio',
                'nivel' => 'Directivo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Subdirector Administrativo',
                'descripcion' => 'Responsable de la gestión administrativa e institucional',
                'nivel' => 'Directivo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            /*
            |------
            | NIVEL ADMINISTRATIVO Y ACADÉMICO
            |------
            */
            
            [
                'nombre' => 'Secretaria General',
                'descripcion' => 'Responsable de secretaría general y correspondencia',
                'nivel' => 'Administrativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Asistente de Dirección',
                'descripcion' => 'Asistente de dirección y coordinación de actividades',
                'nivel' => 'Administrativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Asesor Estratégico',
                'descripcion' => 'Asesor en planificación estratégica e institucional',
                'nivel' => 'Administrativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Jefe de Programa Académico',
                'descripcion' => 'Responsable de la administración de programas académicos',
                'nivel' => 'Académico',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Coordinador de Maestría',
                'descripcion' => 'Coordinador de programas de maestría y especialización',
                'nivel' => 'Académico',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Coordinador de Investigación',
                'descripcion' => 'Coordinador de investigación y proyectos académicos',
                'nivel' => 'Académico',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Docente Investigador',
                'descripcion' => 'Docente con funciones de investigación académica',
                'nivel' => 'Académico',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Docente',
                'descripcion' => 'Docente de cursos y materias en postgrado',
                'nivel' => 'Académico',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            /*
            |------
            | NIVEL OPERATIVO - ADMINISTRATIVO
            |------
            */
            
            [
                'nombre' => 'Encargado de Recepción',
                'descripcion' => 'Responsable de la ventanilla de recepción y correspondencia',
                'nivel' => 'Operativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Recepcionista',
                'descripcion' => 'Atención al público y recepción de documentos',
                'nivel' => 'Operativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Auxiliar de Recepción',
                'descripcion' => 'Auxiliar en actividades de recepción y correspondencia',
                'nivel' => 'Operativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Especialista Administrativo',
                'descripcion' => 'Especialista en procesos administrativos y documentales',
                'nivel' => 'Operativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Especialista Financiero',
                'descripcion' => 'Especialista en gestión financiera y presupuestaria',
                'nivel' => 'Operativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Especialista en RRHH',
                'descripcion' => 'Especialista en recursos humanos y gestión del personal',
                'nivel' => 'Operativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Auxiliar Administrativo',
                'descripcion' => 'Auxiliar general en tareas administrativas',
                'nivel' => 'Operativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Auxiliar de Servicios',
                'descripcion' => 'Auxiliar de servicios generales y mantenimiento',
                'nivel' => 'Operativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            /*
            |------
            | NIVEL OPERATIVO - TÉCNICO
            |------
            */
            
            [
                'nombre' => 'Especialista en Tecnología',
                'descripcion' => 'Especialista en sistemas informáticos y tecnología',
                'nivel' => 'Operativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Técnico en Informática',
                'descripcion' => 'Técnico de soporte informático y redes',
                'nivel' => 'Operativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'nombre' => 'Bibliotecario',
                'descripcion' => 'Responsable de la biblioteca y centro de recursos',
                'nivel' => 'Operativo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
