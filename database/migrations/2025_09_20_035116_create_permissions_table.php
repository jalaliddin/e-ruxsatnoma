<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('permissions', function (Blueprint $table) {
        $table->id(); // ruxsatnoma raqami
        $table->unsignedBigInteger('user_id'); // kimdan
        $table->string('employee_name'); // kimga
        $table->string('destination'); // qayerga
        $table->timestamp('created_at')->useCurrent(); // sana va vaqt
        $table->dateTime('from_time');
        $table->dateTime('to_time');
        $table->string('code', 4)->unique(); // 4 xonali kod
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
