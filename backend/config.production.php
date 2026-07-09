<?php
/**
 * backend/config.php — TEMPLATE UNTUK HOSTING ONLINE
 * 
 * Ganti nilai di bawah dengan data dari cPanel hosting Anda.
 * (InfinityFree / Hostinger / Niagahoster / dll)
 */

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

// ============================================================
//  Konfigurasi Database
//  → Isi sesuai data MySQL dari cPanel hosting Anda
// ============================================================
define('DB_HOST',    'localhost');          // biasanya tetap 'localhost'
define('DB_PORT',    '3306');
define('DB_NAME',    'NAMA_DATABASE');      // ← ganti ini
define('DB_USER',    'USERNAME_DATABASE');  // ← ganti ini
define('DB_PASS',    'PASSWORD_DATABASE');  // ← ganti ini
define('DB_CHARSET', 'utf8mb4');

// ============================================================
//  Konfigurasi Session
// ============================================================
define('SESSION_LIFETIME', 3600);
define('SESSION_NAME',     'MTS_SESSION');

// ============================================================
//  Inisialisasi Session
// ============================================================
function init_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

// ============================================================
//  Koneksi PDO
// ============================================================
function get_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

// ============================================================
//  Helpers
// ============================================================
function json_response(array $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
function require_method(string $method): void {
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method))
        json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
}
