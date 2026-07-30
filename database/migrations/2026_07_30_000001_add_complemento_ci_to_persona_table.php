<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('PERSONA', 'complemento_ci')) {
            Schema::table('PERSONA', function (Blueprint $table) {
                $table->string('complemento_ci', 10)
                    ->nullable()
                    ->after('ci')
                    ->comment('Complemento opcional del carnet de identidad. Ej: LP, CB, 1A');
            });
        }

        $this->dropCiUniqueIndex();
        DB::statement('ALTER TABLE PERSONA MODIFY ci VARCHAR(50) NULL');
    }

    public function down(): void
    {
        if (Schema::hasColumn('PERSONA', 'complemento_ci')) {
            Schema::table('PERSONA', function (Blueprint $table) {
                $table->dropColumn('complemento_ci');
            });
        }
    }

    private function dropCiUniqueIndex(): void
    {
        $indexes = DB::select("SHOW INDEX FROM PERSONA WHERE Column_name = 'ci' AND Non_unique = 0");

        foreach ($indexes as $index) {
            if (!empty($index->Key_name) && $index->Key_name !== 'PRIMARY') {
                DB::statement('ALTER TABLE PERSONA DROP INDEX ' . $index->Key_name);
            }
        }
    }
};
