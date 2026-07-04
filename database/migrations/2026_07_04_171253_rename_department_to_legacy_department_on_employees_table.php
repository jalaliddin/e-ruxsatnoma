<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 'department' (free-text) o'rnini endi 'department_id' (departments
        // jadvaliga FK) egallaydi. Eski matn qiymatlarni yo'qotmaslik uchun
        // ustunni o'chirmay, nomini o'zgartiramiz (Eloquent'da 'department'
        // relation nomi bilan to'qnashmasligi uchun ham kerak).
        DB::statement('ALTER TABLE employees CHANGE department legacy_department VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE employees CHANGE legacy_department department VARCHAR(255) NULL');
    }
};
