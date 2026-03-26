<?php
// database/migrations/2026_03_26_224050_add_default_status_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ STEP 1: Add the column FIRST (if it doesn't exist)
        if (!Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('status')->default('active')->after('role');
            });
        }

        // ✅ STEP 2: Now update existing null/empty statuses
        DB::table('users')
            ->whereNull('status')
            ->orWhere('status', '')
            ->update(['status' => 'active']);

        // ✅ STEP 3: Ensure column has proper default (idempotent)
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->nullable()->change();
        });
    }
};
