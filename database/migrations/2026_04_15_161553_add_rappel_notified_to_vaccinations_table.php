<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_rappel_notified_to_vaccinations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vaccinations', function (Blueprint $table) {
            $table->boolean('rappel_notified')->default(false)->after('rappel_prevu');
        });
    }

    public function down(): void
    {
        Schema::table('vaccinations', function (Blueprint $table) {
            $table->dropColumn('rappel_notified');
        });
    }
};