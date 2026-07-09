<?php
/**
 * backend/login.php
 * Endpoint: POST /backend/login.php
 * Body (JSON): { "username": "...", "password": "..." }
 * Response   : { "success": bool, "message": "...", "user": {...} }
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

// Header CORS untuk pengembangan lokal — hapus/sesuaikan di production
header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_ORIGIN']) ? htmlspecialchars($_SERVER['HTTP_ORIGIN'], ENT_QUOTES) : '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: POST, OPTIONS');

// Preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

init_session();
require_method('POST');

// ------------------------------------------------------------------
// 1. Ambil & validasi input
// ------------------------------------------------------------------
$body = (string) file_get_contents('php://input');
$data = json_decode($body, true);

$username = trim((string) ($data['username'] ?? ''));
$password = (string) ($data['password'] ?? '');

if ($username === '' || $password === '') {
    json_response(['success' => false, 'message' => 'Username dan password tidak boleh kosong.'], 422);
}

// Batasi panjang input untuk mencegah serangan ukuran
if (strlen($username) > 100 || strlen($password) > 255) {
    json_response(['success' => false, 'message' => 'Input tidak valid.'], 422);
}

// ------------------------------------------------------------------
// 2. Rate limiting sederhana berbasis session
//    (Untuk production, gunakan rate limiting di level server/Redis)
// ------------------------------------------------------------------
$now = time();
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['login_lockout_until'] = 0;
}

if ($now < (int) $_SESSION['login_lockout_until']) {
    $sisa = (int) $_SESSION['login_lockout_until'] - $now;
    json_response([
        'success' => false,
        'message' => "Terlalu banyak percobaan gagal. Coba lagi dalam {$sisa} detik.",
    ], 429);
}

// ------------------------------------------------------------------
// 3. Cari user di database
// ------------------------------------------------------------------
try {
    $pdo  = get_db();
    $stmt = $pdo->prepare(
        'SELECT id, username, email, full_name, role, password_hash, is_active
         FROM users
         WHERE (username = :username OR email = :email)
         LIMIT 1'
    );
    $stmt->execute([':username' => $username, ':email' => $username]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    error_log('DB Error [login]: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Terjadi kesalahan server. Silakan coba lagi.'], 500);
}

// ------------------------------------------------------------------
// 4. Verifikasi password & status akun
// ------------------------------------------------------------------
if (!$user || !password_verify($password, (string) $user['password_hash'])) {
    $_SESSION['login_attempts']++;

    // Kunci akun selama 5 menit setelah 5 kali gagal
    if ((int) $_SESSION['login_attempts'] >= 5) {
        $_SESSION['login_lockout_until'] = $now + 300;
        $_SESSION['login_attempts']      = 0;
        json_response([
            'success' => false,
            'message' => 'Terlalu banyak percobaan gagal. Akun dikunci selama 5 menit.',
        ], 429);
    }

    $sisa_coba = 5 - (int) $_SESSION['login_attempts'];
    json_response([
        'success' => false,
        'message' => "Username/email atau password salah. Sisa percobaan: {$sisa_coba}.",
    ], 401);
}

if (!(bool) $user['is_active']) {
    json_response(['success' => false, 'message' => 'Akun Anda belum aktif. Hubungi administrator.'], 403);
}

// ------------------------------------------------------------------
// 5. Login berhasil — buat session baru (cegah session fixation)
// ------------------------------------------------------------------
session_regenerate_id(true);

$_SESSION['login_attempts']    = 0;
$_SESSION['login_lockout_until'] = 0;
$_SESSION['user_id']           = $user['id'];
$_SESSION['username']          = $user['username'];
$_SESSION['full_name']         = $user['full_name'];
$_SESSION['role']              = $user['role'];
$_SESSION['logged_in_at']      = $now;

// Perbarui last_login di database
try {
    $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = :id')
        ->execute([':id' => $user['id']]);
} catch (PDOException $e) {
    error_log('DB Error [update last_login]: ' . $e->getMessage());
    // Tidak fatal, lanjutkan
}

json_response([
    'success' => true,
    'message' => 'Login berhasil. Selamat datang, ' . htmlspecialchars($user['full_name'], ENT_QUOTES) . '!',
    'user'    => [
        'id'        => $user['id'],
        'username'  => $user['username'],
        'full_name' => $user['full_name'],
        'role'      => $user['role'],
    ],
]);
