-- Patch manual production: konfigurasi percabangan survey
-- Target: MariaDB 10.6 / MySQL 8
-- Aman terhadap data lama: tidak ada DROP, TRUNCATE, atau DELETE.

START TRANSACTION;

CREATE TABLE IF NOT EXISTS `survey_branch_rules` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `group_id` BIGINT UNSIGNED NOT NULL,
    `parent_question_id` BIGINT UNSIGNED NOT NULL,
    `affirmative_option_id` BIGINT UNSIGNED NOT NULL,
    `skip_form_id` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `branch_rule_trigger_unique`
        (`group_id`, `parent_question_id`, `affirmative_option_id`),
    KEY `survey_branch_rules_parent_question_id_foreign` (`parent_question_id`),
    KEY `survey_branch_rules_affirmative_option_id_foreign` (`affirmative_option_id`),
    KEY `survey_branch_rules_skip_form_id_foreign` (`skip_form_id`),
    CONSTRAINT `survey_branch_rules_group_id_foreign`
        FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
    CONSTRAINT `survey_branch_rules_parent_question_id_foreign`
        FOREIGN KEY (`parent_question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
    CONSTRAINT `survey_branch_rules_affirmative_option_id_foreign`
        FOREIGN KEY (`affirmative_option_id`) REFERENCES `options` (`id`) ON DELETE CASCADE,
    CONSTRAINT `survey_branch_rules_skip_form_id_foreign`
        FOREIGN KEY (`skip_form_id`) REFERENCES `forms` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `survey_branch_rule_questions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `survey_branch_rule_id` BIGINT UNSIGNED NOT NULL,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `branch_rule_question_unique` (`survey_branch_rule_id`, `question_id`),
    KEY `survey_branch_rule_questions_question_id_foreign` (`question_id`),
    CONSTRAINT `survey_branch_rule_questions_rule_id_foreign`
        FOREIGN KEY (`survey_branch_rule_id`) REFERENCES `survey_branch_rules` (`id`) ON DELETE CASCADE,
    CONSTRAINT `survey_branch_rule_questions_question_id_foreign`
        FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `survey_branch_rule_skipped_questions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `survey_branch_rule_id` BIGINT UNSIGNED NOT NULL,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `branch_rule_skipped_question_unique`
        (`survey_branch_rule_id`, `question_id`),
    KEY `branch_skip_question_fk` (`question_id`),
    CONSTRAINT `branch_skip_rule_fk`
        FOREIGN KEY (`survey_branch_rule_id`) REFERENCES `survey_branch_rules` (`id`) ON DELETE CASCADE,
    CONSTRAINT `branch_skip_question_fk`
        FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `survey_branch_rule_skipped_forms` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `survey_branch_rule_id` BIGINT UNSIGNED NOT NULL,
    `form_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `branch_rule_skipped_form_unique` (`survey_branch_rule_id`, `form_id`),
    KEY `branch_skip_form_fk` (`form_id`),
    CONSTRAINT `branch_skip_form_rule_fk`
        FOREIGN KEY (`survey_branch_rule_id`) REFERENCES `survey_branch_rules` (`id`) ON DELETE CASCADE,
    CONSTRAINT `branch_skip_form_fk`
        FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Catat sebagai migrasi yang telah diterapkan supaya Laravel tidak membuat ulang.
SET @branch_migration_batch := (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_08_11_110000_create_survey_branch_rules_tables', @branch_migration_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_08_11_110000_create_survey_branch_rules_tables'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_08_11_120000_expand_survey_branch_rules', @branch_migration_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_08_11_120000_expand_survey_branch_rules'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_08_11_130000_add_multiple_skipped_forms_to_survey_branch_rules', @branch_migration_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_08_11_130000_add_multiple_skipped_forms_to_survey_branch_rules'
);

COMMIT;

-- Verifikasi setelah eksekusi:
SELECT `migration`, `batch`
FROM `migrations`
WHERE `migration` LIKE '2026_08_11_%'
ORDER BY `migration`;

SELECT TABLE_NAME, ENGINE
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN (
      'survey_branch_rules',
      'survey_branch_rule_questions',
      'survey_branch_rule_skipped_questions',
      'survey_branch_rule_skipped_forms'
  )
ORDER BY TABLE_NAME;
