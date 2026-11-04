<?php

declare(strict_types=1);

use Careminate\Database\SqlConnection;

return static function (SqlConnection $database): void {
    $database->execute(<<<'SQL'
        CREATE TABLE `tasks` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` BIGINT UNSIGNED NOT NULL,
            `title` VARCHAR(200) NOT NULL,
            `description` TEXT NULL,
            `status` VARCHAR(16) CHARACTER SET ascii COLLATE ascii_bin NOT NULL
                DEFAULT 'pending',
            `created_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
            `updated_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
                ON UPDATE CURRENT_TIMESTAMP(6),
            PRIMARY KEY (`id`),
            KEY `idx_tasks_user_id_id` (`user_id`, `id`),
            KEY `idx_tasks_user_status_id` (`user_id`, `status`, `id`),
            CONSTRAINT `fk_tasks_user` FOREIGN KEY (`user_id`)
                REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
            CONSTRAINT `chk_tasks_status`
                CHECK (`status` IN ('pending', 'in_progress', 'completed')),
            CONSTRAINT `chk_tasks_title`
                CHECK (CHAR_LENGTH(TRIM(`title`)) > 0)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
};
