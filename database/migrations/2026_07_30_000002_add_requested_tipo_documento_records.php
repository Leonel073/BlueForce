<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->tipos() as $tipo) {
            DB::table('TIPO_DOCUMENTO')->updateOrInsert(
                ['nombre' => $tipo],
                ['nombre' => $tipo]
            );
        }
    }

    public function down(): void
    {
        DB::table('TIPO_DOCUMENTO')
            ->whereIn('nombre', $this->tipos())
            ->delete();
    }

    private function tipos(): array
    {
        return [
            'Oficio',
            'Correo Electronicos',
            'FAX',
            'Notas de Servicio',
            'Certificado',
        ];
    }
};
