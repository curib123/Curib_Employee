-- database/add_user_auth.sql | 2026-09-21
-- Safe migration for an existing Curib database that already has the employee table.

USE `Curib`;

CREATE TABLE IF NOT EXISTS `users` (
    `Id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `firstname` VARCHAR(100) NOT NULL,
    `lastname` VARCHAR(100) NOT NULL,
    `birthday` DATE NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `contactno` VARCHAR(20) NOT NULL,
    `email` VARCHAR(190) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`Id`),
    UNIQUE KEY `uq_users_email` (`email`),
    INDEX `idx_users_name` (`lastname`, `firstname`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
