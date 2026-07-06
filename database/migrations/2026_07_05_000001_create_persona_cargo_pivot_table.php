<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('PERSONA_CARGO', function (Blueprint $table) {
            $table->id('idPersonaCargo');
            $table->unsignedBigInteger('idPersona');
            $table->unsignedBigInteger('idCargo');
            $table->boolean('activo')->default(true);
            $table->boolean('principal')->default(false);
            $table->timestamp('fecha_asignacion')->nullable();
            $table->timestamps();

            $table->foreign('idPersona')
                  ->references('idPersona')
                  ->on('PERSONA')
                  ->onDelete('cascade');

            $table->foreign('idCargo')
                  ->references('idCargo')
                  ->on('CARGO')
                  ->onDelete('cascade');

            $table->unique(['idPersona', 'idCargo'], 'uk_persona_cargo');
        });

        // Migrar datos existentes de PERSONA.idCargo a la tabla pivote
        $personas = DB::table('PERSONA')
            ->whereNotNull('idCargo')
            ->select('idPersona', 'idCargo')
            ->get();

        foreach ($personas as $p) {
            DB::table('PERSONA_CARGO')->insert([
                'idPersona'        => $p->idPersona,
                'idCargo'          => $p->idCargo,
                'activo'           => true,
                'principal'        => true,
                'fecha_asignacion' => now(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('PERSONA_CARGO');
    }
};
