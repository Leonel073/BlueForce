<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCargoTable extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        /*
        |--------------------------------------------------------------------------
        | TABLA CARGO
        |--------------------------------------------------------------------------
        | Catálogo de cargos para la Escuela de Postgrado de la Armada Boliviana
        | Una persona puede tener UN cargo o estar sin cargo asignado
        |--------------------------------------------------------------------------
        */

        Schema::create('CARGO', function (Blueprint $table) {
            
            $table->id('idCargo');
            
            $table->string('nombre', 150)
                  ->unique()
                  ->comment('Nombre del cargo (ej: Director General, Recepcionista)');
            
            $table->text('descripcion')
                  ->nullable()
                  ->comment('Descripción detallada del cargo y sus funciones');
            
            $table->string('nivel', 50)
                  ->nullable()
                  ->comment('Nivel jerárquico (Directivo, Administrativo, Operativo)');
            
            $table->boolean('activo')
                  ->default(true)
                  ->comment('Indica si el cargo está activo en el sistema');
            
            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | MODIFICAR TABLA PERSONA - AGREGAR FK A CARGO
        |--------------------------------------------------------------------------
        */

        Schema::table('PERSONA', function (Blueprint $table) {
            
            // Agregar columna idCargo después de institucion
            $table->unsignedBigInteger('idCargo')
                  ->nullable()
                  ->after('institucion')
                  ->comment('Referencia al cargo de la persona');
            
            // Agregar foreign key
            $table->foreign('idCargo')
                  ->references('idCargo')
                  ->on('CARGO')
                  ->onDelete('set null')
                  ->comment('FK: PERSONA.idCargo -> CARGO.idCargo');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();

        // Eliminar FK de PERSONA
        Schema::table('PERSONA', function (Blueprint $table) {
            $table->dropForeignKey(['idCargo']);
            $table->dropColumn('idCargo');
        });

        // Eliminar tabla CARGO
        Schema::dropIfExists('CARGO');

        Schema::enableForeignKeyConstraints();
    }
}
