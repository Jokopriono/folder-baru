<?php
/**
 * backend/change_password.php
 * Endpoint: POST /backend/change_password.php
 * Body (JSON): { "current_password": "...", "new_password": "...", "confirm_password": "..." }
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_ORIGIN']) ? htmlspecialchars($_SERVER['HTTP_ORIGIN'], ENT_QUOTES) : '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

init_session();
require_method('POST');

if (empty($_SESSION['user_id'])) json_response(['success' => false, 'message' => 'Unauthorized.'], 401);

$body            = json_decode((string) file_get_contents('php://input'), true);
$current_pw      = (string)($body['current_password']  ?? '');
$new_pw          = (string)($body['new_password']       ?? '');
$confirm_pw      = (string)($body['confirm_password']   ?? '');

// Validasi input
if (!$current_pw || !$new_pw || !$confirm_pw)
    json_response(['success' => false, 'message' => 'Semua field harus diisi.'], 422);
if (strlen($new_pw) < 8)
    json_response(['success' => false, 'message' => 'Password baru minimal 8 karakter.'], 422);
if ($new_pw !== $confirm_pw)
    json_response(['success' => false, 'message' => 'Konfirmasi password tidak cocok.'], 422);
if ($current_pw === $new_pw)
    json_response(['success' => false, 'message' => 'Password baru tidak boleh sama dengan password lama.'], 422);

try {
    $pdo  = get_db();
    $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current_pw, (string)$user['password_hash']))
        json_response(['success' => false, 'message' => 'Password saat ini salah.'], 401);

    $new_hash = password_hash($new_pw, PASSWORD_BCRYPT, ['cost' => 12]);
    $pdo->prepare('UPDATE users SET password_hash = :hash WHERE id = :id')
        ->execute([':hash' => $new_hash, ':id' => $_SESSION['user_id']]);

    json_response(['success' => true, 'message' => 'Password berhasil diperbarui.']);

} catch (PDOException $e) {
    error_log('change_password DB error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Terjadi kesalahan server.'], 500);
}
