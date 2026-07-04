<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->after('user_id')->constrained('employees')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->after('employee_id')->constrained('permission_categories')->nullOnDelete();
            $table->text('reason')->nullable()->after('destination');
            $table->enum('status', ['pending', 'awaiting_manager', 'approved', 'rejected'])->default('approved')->after('code');
            $table->foreignId('approver_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('hr_id')->nullable()->after('approver_id')->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable()->after('hr_id');
        });

        // Existing columns were NOT NULL; requests created from the bot don't
        // have these values until later steps of the workflow (no doctrine/dbal
        // dependency, so this is done with raw SQL instead of change()).
        DB::statement('ALTER TABLE permissions MODIFY user_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE permissions MODIFY employee_name VARCHAR(255) NULL');
        DB::statement('ALTER TABLE permissions MODIFY destination VARCHAR(255) NULL');
        DB::statement('ALTER TABLE permissions MODIFY code VARCHAR(4) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['approver_id']);
            $table->dropForeign(['hr_id']);
            $table->dropColumn(['employee_id', 'category_id', 'reason', 'status', 'approver_id', 'hr_id', 'decided_at']);
        });
    }
};
