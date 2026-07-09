-- ============================================================
--  Schema Database: mts_bireuen
--  Jalankan file ini sekali di phpMyAdmin / MySQL CLI:
--    mysql -u root -p < database/schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS mts_bireuen
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE mts_bireuen;

-- ============================================================
--  Tabel users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    username      VARCHAR(50)     NOT NULL UNIQUE,
    email         VARCHAR(100)    NOT NULL UNIQUE,
    full_name     VARCHAR(150)    NOT NULL,
    password_hash VARCHAR(255)    NOT NULL,
    role          ENUM('admin','guru','siswa','operator') NOT NULL DEFAULT 'siswa',
    is_active     TINYINT(1)      NOT NULL DEFAULT 1,
    last_login    DATETIME        NULL,
    created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_username (username),
    INDEX idx_email    (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Akun admin dibuat otomatis oleh backend/setup.php
--  Jalankan: http://localhost/backend/setup.php (lalu HAPUS file tersebut!)
-- ============================================================
