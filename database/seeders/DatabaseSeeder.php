<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Setting;
use App\Models\SubscriptionPlan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Run individual seeders that actually exist
        $this->call([
            // Uncomment/add seeders as you create them:
            // SettingsSeeder::class,
            // SubscriptionPlansSeeder::class,
            // UsersSeeder::class,
        ]);

        // ✅ Default admin user (for testing)
        $this->createAdminUser();

        // ✅ Default settings
        $this->createDefaultSettings();

        // ✅ Default subscription plans
        $this->createSubscriptionPlans();
    }

    private function createAdminUser(): void
    {
        if (!User::where('email', 'admin@cuniapp.com')->exists()) {
            User::create([
                'name' => 'Admin CuniApp',
                'email' => 'admin@cuniapp.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role' => 'admin',
                'subscription_status' => 'active',
                'subscription_ends_at' => now()->addYear(),
                'theme' => 'light',
                'language' => 'fr',
            ]);
        }

        // ✅ Default test user
        if (!User::where('email', 'user@cuniapp.com')->exists()) {
            User::create([
                'name' => 'Utilisateur Test',
                'email' => 'user@cuniapp.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role' => 'user',
                'subscription_status' => 'active',
                'subscription_ends_at' => now()->addMonth(),
                'theme' => 'system',
                'language' => 'fr',
            ]);
        }
    }

    private function createDefaultSettings(): void
    {
        $settings = [
            ['key' => 'farm_name', 'value' => 'Ferme CuniApp', 'type' => 'string', 'group' => 'general'],
            ['key' => 'gestation_days', 'value' => '31', 'type' => 'number', 'group' => 'breeding'],
            ['key' => 'weaning_weeks', 'value' => '6', 'type' => 'number', 'group' => 'breeding'],
            ['key' => 'verification_initial_days', 'value' => '10', 'type' => 'number', 'group' => 'breeding'],
            ['key' => 'verification_reminder_days', 'value' => '15', 'type' => 'number', 'group' => 'breeding'],
            ['key' => 'verification_interval_days', 'value' => '5', 'type' => 'number', 'group' => 'breeding'],
            ['key' => 'default_price_male', 'value' => '25000', 'type' => 'number', 'group' => 'sales'],
            ['key' => 'default_price_female', 'value' => '30000', 'type' => 'number', 'group' => 'sales'],
            ['key' => 'default_price_lapereau', 'value' => '15000', 'type' => 'number', 'group' => 'sales'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                array_merge($setting, ['label' => $setting['key']])
            );
        }
    }

    private function createSubscriptionPlans(): void
    {
        $plans = [
            ['name' => 'Mensuel', 'duration_months' => 1, 'price' => 2500, 'is_active' => true],
            ['name' => 'Trimestriel', 'duration_months' => 3, 'price' => 7500, 'is_active' => true],
            ['name' => 'Semestriel', 'duration_months' => 6, 'price' => 15000, 'is_active' => true],
            ['name' => 'Annuel', 'duration_months' => 12, 'price' => 30000, 'is_active' => true],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
} 