-- database/curib.sql | 2026-09-21
-- Database and employee table for the Curib Employee CRUD application.

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
