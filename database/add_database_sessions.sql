-- database/add_database_sessions.sql | 2026-09-22
-- Adds CodeIgniter database sessions and persistent login throttling to an existing Curib database.

USE `Curib`;

CREATE TABLE IF NOT EXISTS `ci_sessions` (
    `id` VARCHAR(128) NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `timestamp` INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `data` BLOB NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `login_attempts` (
    `identifier_hash` CHAR(64) NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `attempt_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `last_attempt_at` INT UNSIGNED NOT NULL DEFAULT 0,
    `locked_until` INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`identifier_hash`, `ip_address`),
    INDEX `idx_login_attempts_locked_until` (`locked_until`),
    INDEX `idx_login_attempts_last_attempt` (`last_attempt_at`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
