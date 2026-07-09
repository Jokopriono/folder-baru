<?php
// Sembunyikan semua PHP error dari output (kirim ke log saja)
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

// ============================================================
//  Konfigurasi Database — sesuaikan dengan environment Anda
// ============================================================
define('DB_HOST',    'localhost');
define('DB_PORT',    '3306');
define('DB_NAME',    'mts_bireuen');
define('DB_USER',    'root');          // ganti dengan user MySQL Anda
define('DB_PASS',    '');             // ganti dengan password MySQL Anda
define('DB_CHARSET', 'utf8mb4');

// ============================================================
//  Konfigurasi Session
// ============================================================
define('SESSION_LIFETIME', 3600);    // 1 jam (detik)
define('SESSION_NAME',     'MTS_SESSION');

// ============================================================
//  Inisialisasi Session yang Aman
// ============================================================
function init_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),   // hanya HTTPS jika live
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

// ============================================================
//  Koneksi PDO (lazy singleton)
// ============================================================
function get_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

// ============================================================
//  Helper: Kirim JSON response
// ============================================================
function json_response(array $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================================
//  Helper: Hanya izinkan metode tertentu
// ============================================================
function require_method(string $method): void {
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
        json_response(['success' => false, 'message' => 'Method not allowed.'], 405);
    }
}
