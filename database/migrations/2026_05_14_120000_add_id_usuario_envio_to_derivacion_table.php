<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Usuario del sistema que registró o envió la derivación (auditoría).
     * Distinto de idUsuarioAsignado (usuario/persona destino cuando aplica).
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('DERIVACION', function (Blueprint $table) {
            $table->unsignedBigInteger('idUsuarioEnvio')
                ->nullable()
                ->after('idUsuarioAsignado')
                ->comment('Usuario que realizó la derivación');

            $table->foreign('idUsuarioEnvio')
                ->references('id')
                ->on('users');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('DERIVACION', function (Blueprint $table) {
            $table->dropForeign(['idUsuarioEnvio']);
            $table->dropColumn('idUsuarioEnvio');
        });

        Schema::enableForeignKeyConstraints();
    }
};
