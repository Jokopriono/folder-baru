<?php
/**
 * backend/session_check.php
 * Endpoint: GET /backend/session_check.php
 * Dipakai oleh halaman HTML untuk memeriksa apakah pengguna sudah login.
 * Response: { "logged_in": bool, "user": {...} | null }
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_ORIGIN']) ? htmlspecialchars($_SERVER['HTTP_ORIGIN'], ENT_QUOTES) : '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

init_session();
require_method('GET');

$logged_in = !empty($_SESSION['user_id']);

if ($logged_in) {
    // Periksa apakah session sudah kadaluarsa
    $elapsed = time() - (int) ($_SESSION['logged_in_at'] ?? 0);
    if ($elapsed > SESSION_LIFETIME) {
        session_destroy();
        json_response(['logged_in' => false, 'user' => null]);
    }

    json_response([
        'logged_in' => true,
        'user'      => [
            'id'        => $_SESSION['user_id'],
            'username'  => $_SESSION['username'],
            'full_name' => $_SESSION['full_name'],
            'role'      => $_SESSION['role'],
        ],
    ]);
}

json_response(['logged_in' => false, 'user' => null]);
