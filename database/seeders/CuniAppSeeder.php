<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Male;
use App\Models\Femelle;
use App\Models\Saillie;
use App\Models\MiseBas;
use App\Models\Naissance;
use App\Models\Lapereau;
use App\Models\Sale;
use App\Models\SaleRabbit;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\PaymentTransaction;

class CuniAppDatabaseSeeder extends Seeder
{
    /**
     * Configuration constants for seed data generation
     */
    private const TOTAL_USERS = 50;
    private const TOTAL_MALES = 300;
    private const TOTAL_FEMELLES = 400;
    private const TOTAL_SAILLIES = 500;
    private const TOTAL_MISES_BAS = 350;
    private const TOTAL_NAISSANCES = 350;
    private const TOTAL_LAPEREAUX = 2500;
    private const TOTAL_SALES = 200;
    private const TOTAL_NOTIFICATIONS = 1000;

    /**
     * Sample data arrays for realistic generation
     */
    private array $races = [
        'Géant des Flandres',
        'Californien',
        'Blanc de Vienne',
        'Bleu de Vienne',
        'Rex',
        'Angora',
        'Lionhead',
        'Nain de couleur',
        'Argenté de Champagne',
        'Bourgeois',
        'Nouvelle-Zélande Blanc',
        'Nouvelle-Zélande Rouge',
        'Chinchilla',
        'Havane',
        'Russe',
        'Papillon',
        'Fauve de Bourgogne',
        'Gris de Vienne',
        'Beveren',
        'Checkered Giant'
    ];

    private array $nomsMales = [
        'Max',
        'Rocky',
        'Thunder',
        'Storm',
        'Blaze',
        'Shadow',
        'Ghost',
        'Spirit',
        'Titan',
        'Zeus',
        'Apollo',
        'Hercule',
        'Samson',
        'Goliath',
        'Rex',
        'King',
        'Prince',
        'Duke',
        'Baron',
        'Chef',
        'Boss',
        'Chief',
        'Captain',
        'Commander',
        'General',
        'Major',
        'Colonel',
        'Sergent',
        'Soldat',
        'Guerrier',
        'Champion',
        'Victor',
        'Champion',
        'Winner',
        'Hero',
        'Star',
        'Flash',
        'Lightning',
        'Eclair',
        'Orage',
        'Cyclone',
        'Ouragan',
        'Typhon',
        'Volcan',
        'Séisme',
        'Tonnerre',
        'Foudre',
        'Météore',
        'Comète',
        'Astre',
        'Soleil',
        'Lune',
        'Étoile',
        'Galaxie',
        'Cosmos',
        'Univers',
        'Nébuleuse',
        'Quasar',
        'Pulsar',
        'Trou Noir',
        'Supernova'
    ];

    private array $nomsFemelles = [
        'Luna',
        'Bella',
        'Lily',
        'Daisy',
        'Rose',
        'Jasmine',
        'Violette',
        'Marguerite',
        'Tulipe',
        'Orchidée',
        'Camélia',
        'Pivoine',
        'Iris',
        'Lavande',
        'Mimosa',
        'Cléopâtre',
        'Reine',
        'Princesse',
        'Duchesse',
        'Comtesse',
        'Baronne',
        'Lady',
        'Miss',
        'Madame',
        'Dame',
        'Fée',
        'Sirène',
        'Nymphe',
        'Déesse',
        'Angélique',
        'Sérénité',
        'Harmonie',
        'Mélodie',
        'Symphonie',
        'Rhapsodie',
        'Sonate',
        'Étoile',
        'Lumière',
        'Aurore',
        'Céleste',
        'Divine',
        'Grace',
        'Beauty',
        'Charm',
        'Douceur',
        'Tendresse',
        'Câline',
        'Câlinette',
        'Mignonne',
        'Adorable',
        'Chérie'
    ];

    private array $etatsFemelles = ['Active', 'Gestante', 'Allaitante', 'Vide'];
    private array $etatsMales = ['Active', 'Inactive', 'Malade'];
    private array $palpationResultats = ['+', '-', null];
    private array $etatSante = ['Excellent', 'Bon', 'Moyen', 'Faible'];
    private array $lapereauxEtat = ['vivant', 'vendu', 'mort'];
    private array $lapereauxSex = ['male', 'female'];
    private array $paymentStatus = ['paid', 'pending', 'partial'];
    private array $paymentMethods = ['momo', 'celtis', 'moov', 'manual'];
    private array $subscriptionStatus = ['active', 'expired', 'cancelled', 'pending', 'grace_period'];

    /**
     * User credentials to display after seeding
     */
    private array $userCredentials = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting CuniApp Élevage Database Seeding...');
        $this->command->info('═══════════════════════════════════════════════════════════');

        // Clear existing data (in correct order due to foreign keys)
        $this->clearExistingData();

        // Seed in correct order
        $this->seedSettings();
        $this->seedSubscriptionPlans();
        $this->seedUsers();
        $this->seedMales();
        $this->seedFemelles();
        $this->seedSaillies();
        $this->seedMisesBas();
        $this->seedNaissances();
        $this->seedLapereaux();
        $this->seedSales();
        $this->seedNotifications();
        $this->seedSubscriptions();
        $this->seedPaymentTransactions();

        // Display credentials
        $this->displayCredentials();

        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('═══════════════════════════════════════════════════════════');
    }

    /**
     * Clear existing data in correct order (foreign key constraints)
     */
    private function clearExistingData(): void
    {
        $this->command->info('🗑️  Clearing existing data...');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $tables = [
            'payment_transactions',
            'subscriptions',
            'subscription_plans',
            'sale_rabbits',
            'sales',
            'lapereaux',
            'naissances',
            'mises_bas',
            'saillies',
            'femelles',
            'males',
            'notifications',
            'settings',
            'users',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $this->command->info("   ✓ Truncated: {$table}");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        // Reset auto-increment counters
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE males AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE femelles AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE saillies AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE mises_bas AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE naissances AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE lapereaux AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE sales AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE notifications AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE subscriptions AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE payment_transactions AUTO_INCREMENT = 1');

        $this->command->info('   ✓ Auto-increment counters reset');
        $this->command->newLine();
    }

    /**
     * Seed application settings
     */
    private function seedSettings(): void
    {
        $this->command->info('⚙️  Seeding settings...');

        $settings = [
            // General Settings
            ['key' => 'farm_name', 'value' => 'Ferme CuniApp Élevage', 'type' => 'string', 'group' => 'general', 'label' => 'Nom de la ferme'],
            ['key' => 'farm_address', 'value' => 'Houéyiho après le pont devant Volta United, Cotonou, Littoral, Bénin', 'type' => 'string', 'group' => 'general', 'label' => 'Adresse'],
            ['key' => 'farm_phone', 'value' => '+2290152415241', 'type' => 'string', 'group' => 'general', 'label' => 'Téléphone'],
            ['key' => 'farm_email', 'value' => 'contact@anyxtech.com', 'type' => 'string', 'group' => 'general', 'label' => 'Email'],

            // Breeding Settings
            ['key' => 'gestation_days', 'value' => '31', 'type' => 'number', 'group' => 'breeding', 'label' => 'Jours de gestation'],
            ['key' => 'weaning_weeks', 'value' => '6', 'type' => 'number', 'group' => 'breeding', 'label' => 'Semaines de sevrage'],
            ['key' => 'alert_threshold', 'value' => '80', 'type' => 'number', 'group' => 'breeding', 'label' => "Seuil d'alerte (%)"],

            // Verification Settings
            ['key' => 'verification_initial_days', 'value' => '10', 'type' => 'number', 'group' => 'breeding', 'label' => 'Délai initial de vérification (jours)'],
            ['key' => 'verification_reminder_days', 'value' => '15', 'type' => 'number', 'group' => 'breeding', 'label' => 'Délai premier rappel (jours)'],
            ['key' => 'verification_interval_days', 'value' => '5', 'type' => 'number', 'group' => 'breeding', 'label' => 'Intervalle des rappels (jours)'],

            // Sales Settings
            ['key' => 'default_price_male', 'value' => '25000', 'type' => 'number', 'group' => 'sales', 'label' => 'Prix par défaut - Mâles'],
            ['key' => 'default_price_female', 'value' => '30000', 'type' => 'number', 'group' => 'sales', 'label' => 'Prix par défaut - Femelles'],
            ['key' => 'default_price_lapereau', 'value' => '15000', 'type' => 'number', 'group' => 'sales', 'label' => 'Prix par défaut - Lapereaux'],

            // Payment Settings
            ['key' => 'momo_api_key', 'value' => '', 'type' => 'string', 'group' => 'payments', 'label' => 'MTN MoMo API Key'],
            ['key' => 'momo_api_secret', 'value' => '', 'type' => 'string', 'group' => 'payments', 'label' => 'MTN MoMo API Secret'],
            ['key' => 'momo_environment', 'value' => 'sandbox', 'type' => 'string', 'group' => 'payments', 'label' => 'MTN MoMo Environment'],
            ['key' => 'celtis_api_key', 'value' => '', 'type' => 'string', 'group' => 'payments', 'label' => 'Celtis Cash API Key'],
            ['key' => 'celtis_api_secret', 'value' => '', 'type' => 'string', 'group' => 'payments', 'label' => 'Celtis Cash API Secret'],
            ['key' => 'moov_api_key', 'value' => '', 'type' => 'string', 'group' => 'payments', 'label' => 'Moov Pay API Key'],
            ['key' => 'moov_api_secret', 'value' => '', 'type' => 'string', 'group' => 'payments', 'label' => 'Moov Pay API Secret'],

            // Subscription Settings
            ['key' => 'grace_period_days', 'value' => '3', 'type' => 'number', 'group' => 'subscriptions', 'label' => 'Grace Period (Days)'],
            ['key' => 'enable_auto_renew', 'value' => '1', 'type' => 'boolean', 'group' => 'subscriptions', 'label' => 'Enable Auto-Renewal'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'group' => $setting['group'],
                    'label' => $setting['label'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('   ✓ ' . count($settings) . ' settings created');
        $this->command->newLine();
    }

    /**
     * Seed subscription plans
     */
    private function seedSubscriptionPlans(): void
    {
        $this->command->info('📋 Seeding subscription plans...');

        $plans = [
            [
                'name' => 'Mensuel',
                'duration_months' => 1,
                'price' => 2500.00,
                'is_active' => true,
                'description' => 'Abonnement mensuel pour gérer votre élevage',
                'features' => json_encode([
                    'Nombre illimité de lapins',
                    'Suivi des saillies et mises bas',
                    'Gestion des ventes',
                    'Tableau de bord analytique',
                    'Support par email'
                ])
            ],
            [
                'name' => 'Trimestriel',
                'duration_months' => 3,
                'price' => 7500.00,
                'is_active' => true,
                'description' => 'Abonnement trimestriel avec économie',
                'features' => json_encode([
                    'Nombre illimité de lapins',
                    'Suivi des saillies et mises bas',
                    'Gestion des ventes',
                    'Tableau de bord analytique',
                    'Support prioritaire',
                    'Économie de 15%'
                ])
            ],
            [
                'name' => 'Semestriel',
                'duration_months' => 6,
                'price' => 15000.00,
                'is_active' => true,
                'description' => 'Abonnement semestriel avec avantages',
                'features' => json_encode([
                    'Nombre illimité de lapins',
                    'Suivi des saillies et mises bas',
                    'Gestion des ventes',
                    'Tableau de bord analytique',
                    'Support prioritaire 24/7',
                    'Export de données',
                    'Économie de 25%'
                ])
            ],
            [
                'name' => 'Annuel',
                'duration_months' => 12,
                'price' => 30000.00,
                'is_active' => true,
                'description' => 'Abonnement annuel meilleur rapport qualité-prix',
                'features' => json_encode([
                    'Nombre illimité de lapins',
                    'Suivi des saillies et mises bas',
                    'Gestion des ventes',
                    'Tableau de bord analytique avancé',
                    'Support prioritaire 24/7',
                    'Export de données illimité',
                    'Formations incluses',
                    'Économie de 50%'
                ])
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('subscription_plans')->insert([
                'name' => $plan['name'],
                'duration_months' => $plan['duration_months'],
                'price' => $plan['price'],
                'is_active' => $plan['is_active'],
                'description' => $plan['description'],
                'features' => $plan['features'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('   ✓ ' . count($plans) . ' subscription plans created');
        $this->command->newLine();
    }

    /**
     * Seed users with different roles and subscription statuses
     */
    private function seedUsers(): void
    {
        $this->command->info('👥 Seeding users...');

        // Create admin user
        $admin = User::create([
            'name' => 'Administrateur Principal',
            'email' => 'admin@cuniapp.com',
            'password' => Hash::make('Admin@123456'),
            'email_verified_at' => now(),
            'role' => 'admin',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
            'theme' => 'dark',
            'language' => 'fr',
            'notifications_email' => true,
            'notifications_dashboard' => true,
        ]);

        $this->userCredentials[] = [
            'role' => 'Administrateur',
            'email' => 'admin@cuniapp.com',
            'password' => 'Admin@123456',
            'subscription' => 'Active (1 an)'
        ];

        // Create demo user with active subscription
        $demoUser = User::create([
            'name' => 'Utilisateur Démo',
            'email' => 'demo@cuniapp.com',
            'password' => Hash::make('Demo@123456'),
            'email_verified_at' => now(),
            'role' => 'user',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonths(3),
            'theme' => 'light',
            'language' => 'fr',
            'notifications_email' => true,
            'notifications_dashboard' => true,
        ]);

        $this->userCredentials[] = [
            'role' => 'Utilisateur Démo',
            'email' => 'demo@cuniapp.com',
            'password' => 'Demo@123456',
            'subscription' => 'Active (3 mois)'
        ];

        // Create users with different subscription statuses
        $subscriptionStatuses = [
            ['status' => 'active', 'count' => 20, 'ends_offset' => [30, 365]],
            ['status' => 'expired', 'count' => 10, 'ends_offset' => [-90, -1]],
            ['status' => 'grace_period', 'count' => 5, 'ends_offset' => [-2, 2]],
            ['status' => 'inactive', 'count' => 10, 'ends_offset' => null],
            ['status' => 'pending', 'count' => 4, 'ends_offset' => null],
        ];

        $userCount = 1;

        foreach ($subscriptionStatuses as $statusGroup) {
            for ($i = 0; $i < $statusGroup['count']; $i++) {
                $userCount++;

                $email = "user{$userCount}@cuniapp.com";
                $password = "User@123456";

                $subscriptionEndsAt = null;
                if ($statusGroup['ends_offset']) {
                    $days = rand($statusGroup['ends_offset'][0], $statusGroup['ends_offset'][1]);
                    $subscriptionEndsAt = now()->addDays($days);
                }

                $user = User::create([
                    'name' => "Utilisateur {$userCount}",
                    'email' => $email,
                    'password' => Hash::make($password),
                    'email_verified_at' => rand(0, 1) ? now() : null,
                    'role' => 'user',
                    'subscription_status' => $statusGroup['status'],
                    'subscription_ends_at' => $subscriptionEndsAt,
                    'theme' => ['light', 'dark', 'system'][array_rand(['light', 'dark', 'system'])],
                    'language' => ['fr', 'en'][array_rand(['fr', 'en'])],
                    'notifications_email' => rand(0, 1),
                    'notifications_dashboard' => rand(0, 1),
                ]);

                if ($userCount <= 10) {
                    $this->userCredentials[] = [
                        'role' => "Utilisateur {$userCount}",
                        'email' => $email,
                        'password' => $password,
                        'subscription' => ucfirst($statusGroup['status'])
                    ];
                }
            }
        }

        $this->command->info('   ✓ ' . User::count() . ' users created');
        $this->command->newLine();
    }

    /**
     * Seed male rabbits
     */
    private function seedMales(): void
    {
        $this->command->info('🐰 Seeding male rabbits...');

        $userId = User::where('role', 'admin')->first()->id ?? 1;

        $males = [];
        for ($i = 1; $i <= self::TOTAL_MALES; $i++) {
            $code = 'MAL-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $race = $this->races[array_rand($this->races)];
            $origine = ['Interne', 'Achat'][array_rand(['Interne', 'Achat'])];
            $etat = $this->etatsMales[array_rand($this->etatsMales)];

            // Generate realistic birth dates (0-3 years ago)
            $daysAgo = rand(0, 1095);
            $dateNaissance = now()->subDays($daysAgo);

            $males[] = [
                'code' => $code,
                'nom' => $this->nomsMales[array_rand($this->nomsMales)] . ' ' . $i,
                'race' => $race,
                'origine' => $origine,
                'date_naissance' => $dateNaissance->format('Y-m-d'),
                'etat' => $etat,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now(),
            ];

            // Insert in batches of 100 for performance
            if (count($males) % 100 === 0) {
                DB::table('males')->insert($males);
                $this->command->info("   ✓ Inserted " . count($males) . " males...");
                $males = [];
            }
        }

        // Insert remaining
        if (!empty($males)) {
            DB::table('males')->insert($males);
        }

        $this->command->info('   ✓ ' . self::TOTAL_MALES . ' male rabbits created');
        $this->command->newLine();
    }

    /**
     * Seed female rabbits
     */
    private function seedFemelles(): void
    {
        $this->command->info('🐰 Seeding female rabbits...');

        $femelles = [];
        for ($i = 1; $i <= self::TOTAL_FEMELLES; $i++) {
            $code = 'FEM-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $race = $this->races[array_rand($this->races)];
            $origine = ['Interne', 'Achat'][array_rand(['Interne', 'Achat'])];
            $etat = $this->etatsFemelles[array_rand($this->etatsFemelles)];

            // Generate realistic birth dates (0-3 years ago)
            $daysAgo = rand(0, 1095);
            $dateNaissance = now()->subDays($daysAgo);

            $femelles[] = [
                'code' => $code,
                'nom' => $this->nomsFemelles[array_rand($this->nomsFemelles)] . ' ' . $i,
                'race' => $race,
                'origine' => $origine,
                'date_naissance' => $dateNaissance->format('Y-m-d'),
                'etat' => $etat,
                'created_at' => now()->subDays(rand(0, 365)),
                'updated_at' => now(),
            ];

            // Insert in batches of 100 for performance
            if (count($femelles) % 100 === 0) {
                DB::table('femelles')->insert($femelles);
                $this->command->info("   ✓ Inserted " . count($femelles) . " femelles...");
                $femelles = [];
            }
        }

        // Insert remaining
        if (!empty($femelles)) {
            DB::table('femelles')->insert($femelles);
        }

        $this->command->info('   ✓ ' . self::TOTAL_FEMELLES . ' female rabbits created');
        $this->command->newLine();
    }

    /**
     * Seed matings (saillies)
     */
    private function seedSaillies(): void
    {
        $this->command->info('💕 Seeding matings (saillies)...');

        $maleIds = DB::table('males')->where('etat', 'Active')->pluck('id')->toArray();
        $femelleIds = DB::table('femelles')->pluck('id')->toArray();

        if (empty($maleIds) || empty($femelleIds)) {
            $this->command->error('   ✗ No males or females available for saillies');
            return;
        }

        $saillies = [];
        for ($i = 1; $i <= self::TOTAL_SAILLIES; $i++) {
            $femelleId = $femelleIds[array_rand($femelleIds)];
            $maleId = $maleIds[array_rand($maleIds)];

            // Generate realistic mating dates (0-2 years ago)
            $daysAgo = rand(0, 730);
            $dateSaillie = now()->subDays($daysAgo);

            // Calculate theoretical birth date (31 days after mating)
            $dateMiseBasTheorique = (clone $dateSaillie)->addDays(31);

            // Some saillies have palpation results
            $palpationResultat = rand(0, 10) > 3 ? $this->palpationResultats[array_rand($this->palpationResultats)] : null;
            $datePalpage = $palpationResultat ? (clone $dateSaillie)->addDays(rand(10, 15))->format('Y-m-d') : null;

            $saillies[] = [
                'femelle_id' => $femelleId,
                'male_id' => $maleId,
                'date_saillie' => $dateSaillie->format('Y-m-d'),
                'date_palpage' => $datePalpage,
                'palpation_resultat' => $palpationResultat,
                'date_mise_bas_theorique' => $dateMiseBasTheorique->format('Y-m-d'),
                'created_at' => $dateSaillie->copy()->subDays(rand(0, 5)),
                'updated_at' => now(),
            ];

            // Insert in batches of 100
            if (count($saillies) % 100 === 0) {
                DB::table('saillies')->insert($saillies);
                $this->command->info("   ✓ Inserted " . count($saillies) . " saillies...");
                $saillies = [];
            }
        }

        // Insert remaining
        if (!empty($saillies)) {
            DB::table('saillies')->insert($saillies);
        }

        $this->command->info('   ✓ ' . self::TOTAL_SAILLIES . ' matings created');
        $this->command->newLine();
    }

    /**
     * Seed births (mises bas)
     */
    private function seedMisesBas(): void
    {
        $this->command->info('🥚 Seeding births (mises bas)...');

        // Get saillies with positive palpation results
        $saillieIds = DB::table('saillies')
            ->where('palpation_resultat', '+')
            ->pluck('id')
            ->toArray();

        // Also get some without palpation (direct birth recording)
        $allSaillieIds = DB::table('saillies')->pluck('id')->toArray();
        $saillieIds = array_merge($saillieIds, array_slice($allSaillieIds, 0, 50));
        $saillieIds = array_unique($saillieIds);

        if (empty($saillieIds)) {
            $this->command->error('   ✗ No saillies available for mises bas');
            return;
        }

        $femelleIds = DB::table('femelles')->pluck('id')->toArray();

        $misesBas = [];
        for ($i = 1; $i <= self::TOTAL_MISES_BAS; $i++) {
            $saillieId = $saillieIds[array_rand($saillieIds)];
            $femelleId = $femelleIds[array_rand($femelleIds)];

            // Get saillie date to ensure birth is after mating
            $saillie = DB::table('saillies')->find($saillieId);
            $minBirthDate = $saillie ? Carbon::parse($saillie->date_saillie)->addDays(28) : now()->subDays(365);
            $maxBirthDate = $saillie ? Carbon::parse($saillie->date_saillie)->addDays(35) : now();

            $daysBetween = $minBirthDate->diffInDays($maxBirthDate);
            $dateMiseBas = $minBirthDate->addDays(rand(0, max(1, $daysBetween)));

            // Realistic litter sizes (4-12 rabbits)
            $nbVivant = rand(4, 10);
            $nbMortNe = rand(0, 2);

            // Calculate weaning date (6 weeks after birth)
            $dateSevrage = (clone $dateMiseBas)->addWeeks(6);

            // Average weaning weight (0.5-1.5 kg)
            $poidsMoyenSevrage = rand(500, 1500) / 1000;

            $misesBas[] = [
                'femelle_id' => $femelleId,
                'saillie_id' => $saillieId,
                'date_mise_bas' => $dateMiseBas->format('Y-m-d'),
                'date_sevrage' => $dateSevrage->format('Y-m-d'),
                'poids_moyen_sevrage' => round($poidsMoyenSevrage, 2),
                'created_at' => $dateMiseBas->copy()->subDays(rand(0, 3)),
                'updated_at' => now(),
            ];

            // Insert in batches of 50
            if (count($misesBas) % 50 === 0) {
                DB::table('mises_bas')->insert($misesBas);
                $this->command->info("   ✓ Inserted " . count($misesBas) . " mises bas...");
                $misesBas = [];
            }
        }

        // Insert remaining
        if (!empty($misesBas)) {
            DB::table('mises_bas')->insert($misesBas);
        }

        $this->command->info('   ✓ ' . self::TOTAL_MISES_BAS . ' births created');
        $this->command->newLine();
    }

    /**
     * Seed birth records (naissances)
     */
    private function seedNaissances(): void
    {
        $this->command->info('🐣 Seeding birth records (naissances)...');

        $miseBasIds = DB::table('mises_bas')->pluck('id')->toArray();
        $userId = User::where('role', 'admin')->first()->id ?? 1;

        if (empty($miseBasIds)) {
            $this->command->error('   ✗ No mises bas available for naissances');
            return;
        }

        $naissances = [];
        for ($i = 1; $i <= self::TOTAL_NAISSANCES; $i++) {
            $miseBasId = $miseBasIds[array_rand($miseBasIds)];

            // Get mise bas date
            $miseBas = DB::table('mises_bas')->find($miseBasId);
            $dateNaissance = $miseBas ? Carbon::parse($miseBas->date_mise_bas) : now()->subDays(rand(0, 365));

            // Health status
            $etatSante = $this->etatSante[array_rand($this->etatSante)];

            // Calculate expected dates
            $dateSevragePrevue = (clone $dateNaissance)->addWeeks(6);
            $dateVaccinationPrevue = (clone $dateNaissance)->addWeeks(4);

            // Sex verification tracking
            $sexVerified = rand(0, 10) > 3; // 70% verified
            $sexVerifiedAt = $sexVerified ? $dateNaissance->copy()->addDays(rand(10, 30)) : null;

            // Reminder tracking
            $reminderCount = $sexVerified ? 0 : rand(0, 5);
            $firstReminderSentAt = $reminderCount > 0 ? $dateNaissance->copy()->addDays(15) : null;
            $lastReminderSentAt = $reminderCount > 0 ? $firstReminderSentAt->copy()->addDays($reminderCount * 5) : null;

            $naissances[] = [
                'mise_bas_id' => $miseBasId,
                'user_id' => $userId,
                'poids_moyen_naissance' => rand(40, 80), // grams
                'etat_sante' => $etatSante,
                'observations' => $this->generateObservations(),
                'date_sevrage_prevue' => $dateSevragePrevue->format('Y-m-d'),
                'date_vaccination_prevue' => $dateVaccinationPrevue->format('Y-m-d'),
                'sex_verified' => $sexVerified,
                'sex_verified_at' => $sexVerifiedAt?->format('Y-m-d H:i:s'),
                'first_reminder_sent_at' => $firstReminderSentAt?->format('Y-m-d H:i:s'),
                'last_reminder_sent_at' => $lastReminderSentAt?->format('Y-m-d H:i:s'),
                'reminder_count' => $reminderCount,
                'created_at' => $dateNaissance->copy()->subDays(rand(0, 2)),
                'updated_at' => now(),
            ];

            // Insert in batches of 50
            if (count($naissances) % 50 === 0) {
                DB::table('naissances')->insert($naissances);
                $this->command->info("   ✓ Inserted " . count($naissances) . ' naissances...');
                $naissances = [];
            }
        }

        // Insert remaining
        if (!empty($naissances)) {
            DB::table('naissances')->insert($naissances);
        }

        $this->command->info('   ✓ ' . self::TOTAL_NAISSANCES . ' birth records created');
        $this->command->newLine();
    }

    /**
     * Seed baby rabbits (lapereaux)
     */
    private function seedLapereaux(): void
    {
        $this->command->info('🐇 Seeding baby rabbits (lapereaux)...');

        $naissanceIds = DB::table('naissances')->pluck('id')->toArray();

        if (empty($naissanceIds)) {
            $this->command->error('   ✗ No naissances available for lapereaux');
            return;
        }

        $lapereaux = [];
        $lapereauCounter = 1;

        for ($i = 1; $i <= self::TOTAL_LAPEREAUX; $i++) {
            $naissanceId = $naissanceIds[array_rand($naissanceIds)];

            // Get naissance date for age calculation
            $naissance = DB::table('naissances')->find($naissanceId);
            $miseBas = $naissance ? DB::table('mises_bas')->find($naissance->mise_bas_id) : null;
            $dateNaissance = $miseBas ? Carbon::parse($miseBas->date_mise_bas) : now()->subDays(rand(0, 365));

            // Generate unique code
            $year = $dateNaissance->year;
            $code = "LAP-{$year}-" . str_pad($lapereauCounter, 4, '0', STR_PAD_LEFT);
            $lapereauCounter++;

            // Individual characteristics
            $sex = $this->lapereauxSex[array_rand($this->lapereauxSex)];
            $etat = $this->lapereauxEtat[array_rand($this->lapereauxEtat)];
            $poidsNaissance = rand(40, 90); // grams
            $etatSante = $this->etatSante[array_rand($this->etatSante)];

            // Calculate age categories
            $ageJours = $dateNaissance->diffInDays(now());
            $ageSemaines = floor($ageJours / 7);

            if ($ageSemaines < 5) {
                $categorie = '<5 semaines';
            } elseif ($ageSemaines < 8) {
                $categorie = '5-8 semaines';
            } elseif ($ageSemaines < 12) {
                $categorie = '8-12 semaines';
            } else {
                $categorie = '+12 semaines';
            }

            // Calculate feeding (based on age)
            $alimentationJour = $ageSemaines * 5 + rand(10, 30); // grams per day
            $alimentationSemaine = $alimentationJour * 7;

            $lapereaux[] = [
                'naissance_id' => $naissanceId,
                'code' => $code,
                'nom' => 'Lapereau ' . $lapereauCounter,
                'sex' => $sex,
                'etat' => $etat,
                'poids_naissance' => $poidsNaissance,
                'etat_sante' => $etatSante,
                'observations' => $this->generateLapereauObservations(),
                'categorie' => $categorie,
                'alimentation_jour' => round($alimentationJour / 1000, 2), // kg
                'alimentation_semaine' => round($alimentationSemaine / 1000, 2), // kg
                'created_at' => $dateNaissance->copy()->subDays(rand(0, 2)),
                'updated_at' => now(),
            ];

            // Insert in batches of 200
            if (count($lapereaux) % 200 === 0) {
                DB::table('lapereaux')->insert($lapereaux);
                $this->command->info("   ✓ Inserted " . count($lapereaux) . ' lapereaux...');
                $lapereaux = [];
            }
        }

        // Insert remaining
        if (!empty($lapereaux)) {
            DB::table('lapereaux')->insert($lapereaux);
        }

        $this->command->info('   ✓ ' . self::TOTAL_LAPEREAUX . ' baby rabbits created');
        $this->command->newLine();
    }

    /**
     * Seed sales records
     */
    private function seedSales(): void
    {
        $this->command->info('💰 Seeding sales records...');

        $userIds = DB::table('users')->pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->error('   ✗ No users available for sales');
            return;
        }

        $maleIds = DB::table('males')->where('etat', 'Inactive')->pluck('id')->toArray();
        $femelleIds = DB::table('femelles')->where('etat', 'Vide')->pluck('id')->toArray();
        $lapereauIds = DB::table('lapereaux')->where('etat', 'vendu')->pluck('id')->toArray();

        $allRabbitIds = array_merge(
            array_map(fn($id) => ['type' => 'male', 'id' => $id], $maleIds),
            array_map(fn($id) => ['type' => 'female', 'id' => $id], $femelleIds),
            array_map(fn($id) => ['type' => 'lapereau', 'id' => $id], $lapereauIds)
        );

        $sales = [];
        $saleRabbits = [];

        for ($i = 1; $i <= self::TOTAL_SALES; $i++) {
            $userId = $userIds[array_rand($userIds)];
            $paymentStatus = $this->paymentStatus[array_rand($this->paymentStatus)];

            // Generate sale date (0-1 year ago)
            $dateSale = now()->subDays(rand(0, 365));

            // Select 1-5 rabbits per sale
            $numRabbits = rand(1, 5);
            $selectedRabbits = array_rand($allRabbitIds, min($numRabbits, count($allRabbitIds)));
            if (!is_array($selectedRabbits)) {
                $selectedRabbits = [$selectedRabbits];
            }

            $totalAmount = 0;
            $quantity = 0;

            foreach ($selectedRabbits as $index) {
                $rabbit = $allRabbitIds[$index];

                // Price based on type
                $price = match ($rabbit['type']) {
                    'male' => rand(20000, 35000),
                    'female' => rand(25000, 40000),
                    'lapereau' => rand(10000, 20000),
                    default => 15000
                };

                $totalAmount += $price;
                $quantity++;

                $saleRabbits[] = [
                    'sale_id' => $i, // Will be updated after sale insert
                    'rabbit_type' => $rabbit['type'],
                    'rabbit_id' => $rabbit['id'],
                    'sale_price' => $price,
                    'created_at' => $dateSale->copy()->subDays(rand(0, 2)),
                    'updated_at' => now(),
                ];
            }

            $amountPaid = $paymentStatus === 'paid' ? $totalAmount : ($paymentStatus === 'partial' ? $totalAmount * 0.5 : 0);

            $sales[] = [
                'date_sale' => $dateSale->format('Y-m-d'),
                'quantity' => $quantity,
                'type' => $quantity > 1 ? 'groupe' : ($selectedRabbits[0] ? $allRabbitIds[$selectedRabbits[0]]['type'] : 'lapereau'),
                'category' => null,
                'unit_price' => $quantity > 0 ? round($totalAmount / $quantity, 2) : 0,
                'total_amount' => $totalAmount,
                'buyer_name' => $this->generateBuyerName(),
                'buyer_contact' => $this->generatePhoneNumber(),
                'buyer_address' => $this->generateAddress(),
                'notes' => rand(0, 5) > 3 ? $this->generateSaleNotes() : null,
                'payment_status' => $paymentStatus,
                'amount_paid' => round($amountPaid, 2),
                'user_id' => $userId,
                'created_at' => $dateSale->copy()->subDays(rand(0, 2)),
                'updated_at' => now(),
            ];

            // Insert in batches of 20
            if (count($sales) % 20 === 0) {
                DB::table('sales')->insert($sales);

                // Update sale_rabbits with correct sale_id
                $lastSaleId = DB::table('sales')->orderBy('id', 'desc')->first()->id;
                foreach ($saleRabbits as &$sr) {
                    $sr['sale_id'] = $lastSaleId - (count($sales) - 20) + array_search($sr, $saleRabbits) + 1;
                }

                DB::table('sale_rabbits')->insert(array_splice($saleRabbits, 0, 100));

                $this->command->info("   ✓ Inserted " . count($sales) . ' sales...');
            }
        }

        // Insert remaining
        if (!empty($sales)) {
            $lastInsertedId = DB::table('sales')->insertGetId($sales[0]);
            for ($j = 1; $j < count($sales); $j++) {
                DB::table('sales')->insert($sales[$j]);
                $lastInsertedId++;

                // Insert related sale_rabbits
                $relatedRabbits = array_filter($saleRabbits, fn($sr) => $sr['sale_id'] === $j + count($sales) - count($sales));
                foreach ($relatedRabbits as &$sr) {
                    $sr['sale_id'] = $lastInsertedId;
                }
                if (!empty($relatedRabbits)) {
                    DB::table('sale_rabbits')->insert($relatedRabbits);
                }
            }
        }

        $this->command->info('   ✓ ' . self::TOTAL_SALES . ' sales created');
        $this->command->newLine();
    }

    /**
     * Seed notifications
     */
    private function seedNotifications(): void
    {
        $this->command->info('🔔 Seeding notifications...');

        $userIds = DB::table('users')->pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->error('   ✗ No users available for notifications');
            return;
        }

        $notificationTypes = [
            'success' => [
                'titles' => ['Naissance enregistrée', 'Vente complétée', 'Saillie confirmée', 'Mise bas réussie'],
                'messages' => [
                    'Une nouvelle naissance a été enregistrée avec succès',
                    'La vente a été complétée et le paiement reçu',
                    'La saillie a été confirmée positive',
                    'La mise bas s\'est déroulée sans complication'
                ],
                'icons' => ['bi-check-circle-fill', 'bi-cart-check-fill', 'bi-heart-fill', 'bi-egg-fill']
            ],
            'warning' => [
                'titles' => ['Vérification requise', 'Paiement en attente', 'Santé à surveiller', 'Sevrage prochain'],
                'messages' => [
                    'La vérification du sexe des lapereaux est requise',
                    'Un paiement est en attente de confirmation',
                    'L\'état de santé d\'un lapin nécessite attention',
                    'Le sevrage des lapereaux approche'
                ],
                'icons' => ['bi-exclamation-triangle-fill', 'bi-clock-fill', 'bi-heart-pulse', 'bi-calendar-event']
            ],
            'info' => [
                'titles' => ['Rappel de vaccination', 'Nouvelle fonctionnalité', 'Maintenance prévue', 'Statistiques disponibles'],
                'messages' => [
                    'Rappel: Vaccination prévue cette semaine',
                    'Une nouvelle fonctionnalité est disponible',
                    'Maintenance prévue ce weekend',
                    'Vos statistiques mensuelles sont disponibles'
                ],
                'icons' => ['bi-info-circle-fill', 'bi-star-fill', 'bi-tools', 'bi-graph-up']
            ],
            'error' => [
                'titles' => ['Erreur de paiement', 'Données manquantes', 'Problème de synchronisation', 'Accès refusé'],
                'messages' => [
                    'Le paiement a échoué, veuillez réessayer',
                    'Des données obligatoires sont manquantes',
                    'Problème de synchronisation des données',
                    'Accès refusé à cette ressource'
                ],
                'icons' => ['bi-x-circle-fill', 'bi-file-earmark-x', 'bi-cloud-offline', 'bi-lock-fill']
            ],
        ];

        $notifications = [];
        for ($i = 1; $i <= self::TOTAL_NOTIFICATIONS; $i++) {
            $userId = $userIds[array_rand($userIds)];
            $type = array_rand($notificationTypes);
            $typeData = $notificationTypes[$type];

            $title = $typeData['titles'][array_rand($typeData['titles'])];
            $message = $typeData['messages'][array_rand($typeData['messages'])];
            $icon = $typeData['icons'][array_rand($typeData['icons'])];

            $isRead = rand(0, 10) > 3; // 70% read
            $emailed = rand(0, 10) > 5; // 50% emailed
            $createdAt = now()->subDays(rand(0, 90));
            $readAt = $isRead ? $createdAt->copy()->addMinutes(rand(1, 1440)) : null;

            $notifications[] = [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'action_url' => rand(0, 10) > 3 ? $this->generateActionUrl() : null,
                'icon' => $icon,
                'is_read' => $isRead,
                'emailed' => $emailed,
                'read_at' => $readAt?->format('Y-m-d H:i:s'),
                'created_at' => $createdAt->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];

            // Insert in batches of 200
            if (count($notifications) % 200 === 0) {
                DB::table('notifications')->insert($notifications);
                $this->command->info("   ✓ Inserted " . count($notifications) . ' notifications...');
                $notifications = [];
            }
        }

        // Insert remaining
        if (!empty($notifications)) {
            DB::table('notifications')->insert($notifications);
        }

        $this->command->info('   ✓ ' . self::TOTAL_NOTIFICATIONS . ' notifications created');
        $this->command->newLine();
    }

    /**
     * Seed subscriptions
     */
    private function seedSubscriptions(): void
    {
        $this->command->info('📅 Seeding subscriptions...');

        $userIds = DB::table('users')->where('role', 'user')->pluck('id')->toArray();
        $planIds = DB::table('subscription_plans')->pluck('id')->toArray();

        if (empty($userIds) || empty($planIds)) {
            $this->command->error('   ✗ No users or plans available for subscriptions');
            return;
        }

        $subscriptions = [];
        $subscribedUsers = [];

        // Create subscriptions for 70% of users
        $numSubscriptions = floor(count($userIds) * 0.7);
        $selectedUserIds = array_rand(array_flip($userIds), $numSubscriptions);
        if (!is_array($selectedUserIds)) {
            $selectedUserIds = [$selectedUserIds];
        }

        foreach ($selectedUserIds as $userId) {
            $planId = $planIds[array_rand($planIds)];
            $plan = DB::table('subscription_plans')->find($planId);

            $startDate = now()->subDays(rand(0, 365));
            $endDate = (clone $startDate)->addMonths($plan->duration_months);

            $status = $endDate->isPast() ? 'expired' : 'active';
            if ($status === 'active' && $endDate->diffInDays(now()) <= 3) {
                $status = 'grace_period';
            }

            $subscriptions[] = [
                'user_id' => $userId,
                'subscription_plan_id' => $planId,
                'status' => $status,
                'start_date' => $startDate->format('Y-m-d H:i:s'),
                'end_date' => $endDate->format('Y-m-d H:i:s'),
                'cancelled_at' => $status === 'cancelled' ? now()->format('Y-m-d H:i:s') : null,
                'price' => $plan->price,
                'payment_method' => $this->paymentMethods[array_rand($this->paymentMethods)],
                'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
                'payment_reference' => 'REF-' . strtoupper(Str::random(8)),
                'auto_renew' => rand(0, 10) > 3,
                'cancellation_reason' => $status === 'cancelled' ? $this->generateCancellationReason() : null,
                'created_at' => $startDate->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];

            $subscribedUsers[] = $userId;
        }

        // Insert in batches
        $batches = array_chunk($subscriptions, 50);
        foreach ($batches as $batch) {
            DB::table('subscriptions')->insert($batch);
        }

        $this->command->info('   ✓ ' . count($subscriptions) . ' subscriptions created');
        $this->command->newLine();
    }

    /**
     * Seed payment transactions
     */
    private function seedPaymentTransactions(): void
    {
        $this->command->info('💳 Seeding payment transactions...');

        $userIds = DB::table('users')->pluck('id')->toArray();
        $subscriptionIds = DB::table('subscriptions')->pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->error('   ✗ No users available for payment transactions');
            return;
        }

        $transactions = [];
        $numTransactions = rand(100, 200);

        for ($i = 1; $i <= $numTransactions; $i++) {
            $userId = $userIds[array_rand($userIds)];
            $subscriptionId = !empty($subscriptionIds) ? $subscriptionIds[array_rand($subscriptionIds)] : null;

            $status = $this->subscriptionStatus[array_rand($this->subscriptionStatus)];
            $amount = rand(2500, 30000);

            $transactions[] = [
                'user_id' => $userId,
                'subscription_id' => $subscriptionId,
                'amount' => $amount,
                'payment_method' => $this->paymentMethods[array_rand($this->paymentMethods)],
                'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
                'status' => $status,
                'provider_response' => json_encode([
                    'status' => $status,
                    'message' => 'Payment processed successfully',
                    'timestamp' => now()->toIso8601String()
                ]),
                'provider' => ['mtn', 'celtis', 'moov', 'manual'][array_rand(['mtn', 'celtis', 'moov', 'manual'])],
                'phone_number' => $this->generatePhoneNumber(),
                'failure_reason' => $status === 'failed' ? $this->generateFailureReason() : null,
                'paid_at' => $status === 'completed' ? now()->format('Y-m-d H:i:s') : null,
                'refunded_at' => $status === 'refunded' ? now()->format('Y-m-d H:i:s') : null,
                'created_at' => now()->subDays(rand(0, 365))->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];

            // Insert in batches of 50
            if (count($transactions) % 50 === 0) {
                DB::table('payment_transactions')->insert($transactions);
                $this->command->info("   ✓ Inserted " . count($transactions) . ' transactions...');
                $transactions = [];
            }
        }

        // Insert remaining
        if (!empty($transactions)) {
            DB::table('payment_transactions')->insert($transactions);
        }

        $this->command->info('   ✓ ' . count($transactions) . ' payment transactions created');
        $this->command->newLine();
    }

    /**
     * Display all user credentials in console
     */
    private function displayCredentials(): void
    {
        $this->command->newLine();
        $this->command->info('🔐 USER CREDENTIALS (Save these for testing!)');
        $this->command->info('═══════════════════════════════════════════════════════════');

        foreach ($this->userCredentials as $credential) {
            $this->command->table(
                ['Role', 'Email', 'Password', 'Subscription'],
                [[$credential['role'], $credential['email'], $credential['password'], $credential['subscription']]]
            );
        }

        $this->command->newLine();
        $this->command->info('📊 DATABASE STATISTICS');
        $this->command->info('═══════════════════════════════════════════════════════════');

        $stats = [
            ['Users', User::count()],
            ['Males', Male::count()],
            ['Femelles', Femelle::count()],
            ['Saillies', Saillie::count()],
            ['Mises Bas', MiseBas::count()],
            ['Naissances', Naissance::count()],
            ['Lapereaux', Lapereau::count()],
            ['Sales', Sale::count()],
            ['Notifications', Notification::count()],
            ['Subscriptions', Subscription::count()],
            ['Payment Transactions', PaymentTransaction::count()],
        ];

        $this->command->table(['Entity', 'Count'], $stats);

        $this->command->newLine();
        $this->command->info('💡 QUICK TIPS');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->line('• Admin account has full access to all features');
        $this->command->line('• Demo account has active subscription for testing');
        $this->command->line('• Users with expired subscriptions will see subscription prompts');
        $this->command->line('• Use different accounts to test role-based access');
        $this->command->line('• Check notifications table for test notifications');
        $this->command->line('• Payment transactions include various statuses for testing');
        $this->command->newLine();
    }

    /**
     * Helper: Generate random observations for naissances
     */
    private function generateObservations(): ?string
    {
        $observations = [
            null,
            'Portée en bonne santé, tous les lapereaux sont vigoureux',
            'Quelques lapereaux faibles, surveillance recommandée',
            'Mère attentive, bonne production de lait',
            'Température ambiante optimale maintenue',
            'Alimentation complémentaire à prévoir',
            'Croissance normale observée',
            'Aucune complication particulière',
            'Suivi vétérinaire recommandé',
            'Portée exceptionnelle, tous vivants',
        ];

        return $observations[array_rand($observations)];
    }

    /**
     * Helper: Generate random observations for lapereaux
     */
    private function generateLapereauObservations(): ?string
    {
        $observations = [
            null,
            'Lapereau vigoureux et actif',
            'Poids dans la normale',
            'Développement normal',
            'Appétit bon',
            'Comportement normal',
            'Fourrure en bonne santé',
            'Yeux clairs et brillants',
            'Mobilité bonne',
            'Réactions normales',
        ];

        return $observations[array_rand($observations)];
    }

    /**
     * Helper: Generate random buyer name
     */
    private function generateBuyerName(): string
    {
        $firstNames = ['Jean', 'Marie', 'Paul', 'Sophie', 'Michel', 'Claire', 'Pierre', 'Anne', 'Jacques', 'Isabelle'];
        $lastNames = ['Dupont', 'Martin', 'Bernard', 'Petit', 'Robert', 'Richard', 'Durand', 'Dubois', 'Moreau', 'Laurent'];

        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    /**
     * Helper: Generate random phone number
     */
    private function generatePhoneNumber(): string
    {
        return '+229 ' . rand(94, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99) . ' ' . rand(10, 99);
    }

    /**
     * Helper: Generate random address
     */
    private function generateAddress(): string
    {
        $streets = ['Rue de la Paix', 'Avenue du Commerce', 'Boulevard Central', 'Rue Principale', 'Quartier Résidentiel'];
        $cities = ['Cotonou', 'Porto-Novo', 'Parakou', 'Abomey', 'Ouidah'];

        return rand(1, 100) . ' ' . $streets[array_rand($streets)] . ', ' . $cities[array_rand($cities)];
    }

    /**
     * Helper: Generate sale notes
     */
    private function generateSaleNotes(): ?string
    {
        $notes = [
            null,
            'Client satisfait de la qualité',
            'Livraison prévue la semaine prochaine',
            'Remise accordée pour achat en gros',
            'Premier achat de ce client',
            'Client fidèle, achat régulier',
            'Paiement échelonné convenu',
            'Transport inclus dans le prix',
            'Garantie santé 7 jours',
            'Conseils d\'élevage fournis',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Helper: Generate action URL for notifications
     */
    private function generateActionUrl(): string
    {
        $urls = [
            '/dashboard',
            '/naissances',
            '/sales',
            '/femelles',
            '/males',
            '/saillies',
            '/mises-bas',
            '/notifications',
            '/settings',
            '/profile',
        ];

        return $urls[array_rand($urls)];
    }

    /**
     * Helper: Generate cancellation reason
     */
    private function generateCancellationReason(): string
    {
        $reasons = [
            'Changement de besoins',
            'Problèmes financiers',
            'Insatisfaction du service',
            'Arrêt de l\'activité d\'élevage',
            'Passage à un concurrent',
            'Problèmes techniques récurrents',
            'Manque de fonctionnalités',
            'Prix trop élevé',
            'Support client insuffisant',
            'Autre raison personnelle',
        ];

        return $reasons[array_rand($reasons)];
    }

    /**
     * Helper: Generate payment failure reason
     */
    private function generateFailureReason(): string
    {
        $reasons = [
            'Fonds insuffisants',
            'Carte expirée',
            'Erreur de réseau',
            'Transaction refusée par la banque',
            'Limite de transaction dépassée',
            'Informations de paiement incorrectes',
            'Service de paiement indisponible',
            'Timeout de la transaction',
            'Erreur système',
            'Autre erreur de paiement',
        ];

        return $reasons[array_rand($reasons)];
    }
}
