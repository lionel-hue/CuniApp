-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 03 sep. 2025 à 19:20
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `cuniculturedb`
--

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `femelles`
--

CREATE TABLE `femelles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `race` varchar(255) DEFAULT NULL,
  `origine` enum('Achat','Interne') NOT NULL DEFAULT 'Interne',
  `etat` enum('Active','Gestante','Allaitante','Vide') NOT NULL DEFAULT 'Active',
  `date_naissance` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `femelles`
--

INSERT INTO `femelles` (`id`, `code`, `nom`, `race`, `origine`, `etat`, `date_naissance`, `created_at`, `updated_at`) VALUES
(1, 'F001', 'Luna', 'Néerlandaise', 'Interne', 'Active', '2023-01-10', NULL, NULL),
(2, 'F002', 'Bella', 'Californienne', 'Achat', 'Gestante', '2022-11-05', NULL, NULL),
(3, 'FEM-9091', 'fatima', 'Béninoise', 'Interne', 'Allaitante', '2025-08-14', '2025-08-14 21:28:27', '2025-08-14 22:04:16');

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `lapereaux`
--

CREATE TABLE `lapereaux` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mise_bas_id` bigint(20) UNSIGNED NOT NULL,
  `age_semaines` int(11) DEFAULT NULL,
  `categorie` enum('<5 semaines','5-8 semaines','8-12 semaines','+12 semaines') DEFAULT NULL,
  `alimentation_jour` decimal(6,2) NOT NULL DEFAULT 0.00,
  `alimentation_semaine` decimal(6,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `lapereaux`
--

INSERT INTO `lapereaux` (`id`, `mise_bas_id`, `age_semaines`, `categorie`, `alimentation_jour`, `alimentation_semaine`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '<5 semaines', 0.10, 0.70, NULL, NULL),
(2, 1, 6, '5-8 semaines', 0.20, 1.40, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `males`
--

CREATE TABLE `males` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `race` varchar(100) DEFAULT NULL,
  `origine` enum('Achat','Interne') NOT NULL DEFAULT 'Interne',
  `date_naissance` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `etat` varchar(255) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `males`
--

INSERT INTO `males` (`id`, `code`, `nom`, `race`, `origine`, `date_naissance`, `created_at`, `updated_at`, `etat`) VALUES
(1, 'M001', 'Max', 'Néerlandaise', 'Interne', '2025-08-15', NULL, '2025-08-14 22:22:53', 'Active'),
(2, 'M002', 'Rocky', 'Californienne', 'Achat', '2022-07-20', NULL, '2025-08-14 22:23:06', 'Active'),
(3, 'MAL-5860', 'yoann', 'togolaise', 'Interne', '2025-08-16', '2025-08-16 10:29:27', '2025-08-16 10:29:50', 'Active'),
(4, 'MAL-3759', 'yoann', 'Béninoise', 'Interne', '2025-04-03', '2025-08-20 09:10:07', '2025-08-20 09:10:07', 'active');

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_08_14_091443_create_femelles_table', 1),
(5, '2025_08_14_094400_create_males_table', 1),
(6, '2025_08_14_094418_create_saillies_table', 1),
(7, '2025_08_14_094423_create_mises_bas_table', 1),
(8, '2025_08_14_094431_create_lapereaux_table', 1),
(9, '2025_08_14_094627_add_etat_to_femelles_table', 1),
(10, '2025_08_14_230835_add_etat_to_males_table', 2);

-- --------------------------------------------------------

--
-- Structure de la table `mises_bas`
--

CREATE TABLE `mises_bas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `saillie_id` bigint(20) UNSIGNED NOT NULL,
  `date_mise_bas` date NOT NULL,
  `nb_vivant` int(11) NOT NULL DEFAULT 0,
  `nb_mort_ne` int(11) NOT NULL DEFAULT 0,
  `nb_retire` int(11) NOT NULL DEFAULT 0,
  `nb_adopte` int(11) NOT NULL DEFAULT 0,
  `date_sevrage` date DEFAULT NULL,
  `poids_moyen_sevrage` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `mises_bas`
--

INSERT INTO `mises_bas` (`id`, `saillie_id`, `date_mise_bas`, `nb_vivant`, `nb_mort_ne`, `nb_retire`, `nb_adopte`, `date_sevrage`, `poids_moyen_sevrage`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-09-30', 5, 0, 0, 0, '2025-11-30', 1.25, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `saillies`
--

CREATE TABLE `saillies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `femelle_id` bigint(20) UNSIGNED NOT NULL,
  `male_id` bigint(20) UNSIGNED NOT NULL,
  `date_saillie` date NOT NULL,
  `date_palpage` date DEFAULT NULL,
  `palpation_resultat` enum('+','-') DEFAULT NULL,
  `date_mise_bas_theorique` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `saillies`
--

INSERT INTO `saillies` (`id`, `femelle_id`, `male_id`, `date_saillie`, `date_palpage`, `palpation_resultat`, `date_mise_bas_theorique`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-07-01', '2025-07-15', '+', '2025-08-01', NULL, '2025-08-18 09:19:49'),
(2, 3, 2, '2025-07-05', '2025-07-20', '-', '2025-08-05', NULL, '2025-08-18 08:36:02'),
(3, 2, 1, '2025-08-16', '2025-08-18', '+', '2025-09-16', '2025-08-18 08:36:35', '2025-08-18 08:36:35');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('5KFe9nrLvVFncw4fx9693XykRrSqXDdh0RhxT065', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTkM3OWhCaHYyTmtVRlRPeFJJY2xYOGhvYnd2TW1CMGRJejEzVVVUYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755629544),
('A99lZAL5xYxCqfa1gCVW1hmO6ir1GpvycwkBTje1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiemVvYXNTQU1JNnlhbmxyMktFbnd6N2lZZ3Zva3NuRTlBSGNiMXdFcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756399586),
('EcObyFWiRL6xHlxYdzVAqhwlZRyQv6so7OWy3fnU', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTk11eGNxWEVJV045clVBRU5YMFo4UU01QmJZekV3SWxRZDkxMEVweSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755707349),
('gXMo93g10aBSZNMDCiGZbbWTgorFubMSQzM9Cxoy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWVptcElVWGgwV1Vxd2RMYWQzWU13dGg2dkszc2RQZE94YlFLZ3JIaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tYWxlcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756741957),
('mrXFwyLMcYCVxRI7GMPKLJlW43xhhujUaWfFFj9Z', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZktQdTI2ZHVsaEpBb0JydndXM2JuU0g2S2JtZEpDbWpqOXFQR0RPWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9taXNlcy1iYXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1756460704),
('OOhdkFaQ3acyRIjrZety7KZZkLStfPEyi1emFNTT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZTdXa29RUldoQmQyb0ZhZ2lDRmpMUkdqa013YzhaeTRrNmlMUWVhbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tYWxlcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756315334),
('WCt17NgqopNR9EaGUQJhE0maI2YpYWORjzZQIH9X', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTTA5T3N1MlFHRXFJMTRZQmNCZWN1bU9CNTN6REE5Y0xBNzN3RVg5dSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1756382621),
('YVG9xQDnggUqTY87fqVxtIkwyFeEs1KN1cSpM5xq', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVTREaXRHRkhLQzdES0FJSTRpbjNIeDhIemRoM0thaUNRMUJIT29wTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755717307);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `femelles`
--
ALTER TABLE `femelles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `femelles_code_unique` (`code`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `lapereaux`
--
ALTER TABLE `lapereaux`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lapereaux_mise_bas_id_foreign` (`mise_bas_id`);

--
-- Index pour la table `males`
--
ALTER TABLE `males`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `males_code_unique` (`code`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `mises_bas`
--
ALTER TABLE `mises_bas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mises_bas_saillie_id_foreign` (`saillie_id`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `saillies`
--
ALTER TABLE `saillies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saillies_femelle_id_foreign` (`femelle_id`),
  ADD KEY `saillies_male_id_foreign` (`male_id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `femelles`
--
ALTER TABLE `femelles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `lapereaux`
--
ALTER TABLE `lapereaux`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `males`
--
ALTER TABLE `males`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `mises_bas`
--
ALTER TABLE `mises_bas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `saillies`
--
ALTER TABLE `saillies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `lapereaux`
--
ALTER TABLE `lapereaux`
  ADD CONSTRAINT `lapereaux_mise_bas_id_foreign` FOREIGN KEY (`mise_bas_id`) REFERENCES `mises_bas` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `mises_bas`
--
ALTER TABLE `mises_bas`
  ADD CONSTRAINT `mises_bas_saillie_id_foreign` FOREIGN KEY (`saillie_id`) REFERENCES `saillies` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `saillies`
--
ALTER TABLE `saillies`
  ADD CONSTRAINT `saillies_femelle_id_foreign` FOREIGN KEY (`femelle_id`) REFERENCES `femelles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saillies_male_id_foreign` FOREIGN KEY (`male_id`) REFERENCES `males` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
