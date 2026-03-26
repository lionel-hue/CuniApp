<?php
// database/migrations/2026_XX_XX_add_default_status_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Set default value for existing null statuses
        DB::table('users')
            ->whereNull('status')
            ->orWhere('status', '')
            ->update(['status' => 'active']);

        // Change column to have default value
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
