<?php

declare(strict_types=1);

use Careminate\Database\SqlConnection;

return static function (SqlConnection $database): void {
    $database->execute(<<<'SQL'
        CREATE TABLE `users` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `email` VARCHAR(254) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
            `display_name` VARCHAR(120) NOT NULL,
            `password_hash` VARCHAR(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
            `created_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
            `updated_at` DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
                ON UPDATE CURRENT_TIMESTAMP(6),
            PRIMARY KEY (`id`),
            CONSTRAINT `uq_users_email` UNIQUE (`email`),
            CONSTRAINT `chk_users_email_lower` CHECK (`email` = LOWER(`email`)),
            CONSTRAINT `chk_users_name` CHECK (CHAR_LENGTH(TRIM(`display_name`)) > 0)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);
};
