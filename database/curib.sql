-- database/curib.sql | 2026-09-21
-- Fresh Curib Employee schema with authentication, DB sessions, and login throttling.

CREATE DATABASE IF NOT EXISTS `Curib`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `Curib`;

CREATE TABLE IF NOT EXISTS `employee` (
    `Id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `firstname` VARCHAR(100) NOT NULL,
    `lastname` VARCHAR(100) NOT NULL,
    `birthday` DATE NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `contactno` VARCHAR(20) NOT NULL,
    PRIMARY KEY (`Id`),
    INDEX `idx_employee_name` (`lastname`, `firstname`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
    `Id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `firstname` VARCHAR(100) NOT NULL,
    `lastname` VARCHAR(100) NOT NULL,
    `birthday` DATE NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `contactno` VARCHAR(20) NOT NULL,
    `email` VARCHAR(190) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `must_change_password` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`Id`),
    UNIQUE KEY `uq_users_email` (`email`),
    INDEX `idx_users_name` (`lastname`, `firstname`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

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
