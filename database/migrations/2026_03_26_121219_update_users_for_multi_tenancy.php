<?php
// database/migrations/2026_03_24_000002_update_users_for_multi_tenancy.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add firm_id column
            $table->foreignId('firm_id')->nullable()->after('id')->constrained('firms')->onDelete('set null');
            
            // Index for firm scoping
            $table->index('firm_id');
            $table->index('role');
        });
        
        // ✅ FIX: Convert existing 'admin' roles BEFORE modifying ENUM
        // Map old 'admin' to 'firm_admin' (or 'super_admin' for the first admin)
        $firstAdmin = DB::table('users')->where('role', 'admin')->orderBy('id')->first();
        
        if ($firstAdmin) {
            // First admin becomes super_admin
            DB::table('users')->where('id', $firstAdmin->id)->update(['role' => 'super_admin']);
            
            // Other admins become firm_admin
            DB::table('users')
                ->where('role', 'admin')
                ->where('id', '!=', $firstAdmin->id)
                ->update(['role' => 'firm_admin']);
        }
        
        // ✅ NOW safely modify the ENUM column
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'firm_admin', 'employee', 'user') DEFAULT 'user'");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['firm_id']);
            $table->dropColumn('firm_id');
            
            // Revert ENUM (map firm_admin/super_admin back to admin)
            DB::table('users')->whereIn('role', ['firm_admin', 'super_admin'])->update(['role' => 'admin']);
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin') DEFAULT 'user'");
        });
    }
};