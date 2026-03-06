<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('naissances', function (Blueprint $table) {
            if (Schema::hasColumn('naissances', 'is_archived')) {
                $table->dropColumn('is_archived');
            }
            if (Schema::hasColumn('naissances', 'archived_at')) {
                $table->dropColumn('archived_at');
            }
        });
    }

    public function down(): void {
        Schema::table('naissances', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archived_at')->nullable();
        });
    }
};