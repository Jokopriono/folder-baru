-- ============================================================
--  Schema Database: MTs Muhammadiyah Bireuen
--  Untuk hosting (InfinityFree/cPanel): jalankan langsung di phpMyAdmin
--  Database sudah dipilih otomatis — TIDAK perlu CREATE DATABASE / USE
-- ============================================================

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
--  Tabel news (berita)
-- ============================================================
CREATE TABLE IF NOT EXISTS news (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title        VARCHAR(255) NOT NULL,
    slug         VARCHAR(255) NOT NULL UNIQUE,
    summary      TEXT NOT NULL,
    body         LONGTEXT NOT NULL,
    image_url    VARCHAR(500) DEFAULT NULL,
    author_id    INT UNSIGNED DEFAULT NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_published (is_published, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Tabel contents (pengaturan konten website)
-- ============================================================
CREATE TABLE IF NOT EXISTS contents (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    content_key   VARCHAR(100) NOT NULL UNIQUE,
    content_value TEXT NOT NULL,
    description   VARCHAR(200) DEFAULT NULL,
    updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Data awal konten website
-- ============================================================
INSERT INTO contents (content_key, content_value, description) VALUES
('ticker_1', 'Selamat Datang di Website MTs Muhammadiyah Bireuen', 'Teks ticker 1'),
('ticker_2', 'PPDB 2026/2027 Dibuka', 'Teks ticker 2'),
('ticker_3', 'Saksikan Prestasi Siswa Kami', 'Teks ticker 3'),
('hero_title', 'Mencerdaskan Bangsa', 'Judul hero banner'),
('hero_subtitle', 'Membentuk Generasi Islami, Cerdas, Berkarakter', 'Subjudul hero banner')
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- ============================================================
--  Akun admin dibuat otomatis oleh backend/setup.php
--  Jalankan: https://domain-anda/backend/setup.php (lalu HAPUS!)
-- ============================================================

