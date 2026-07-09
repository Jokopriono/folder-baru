<?php
/**
 * backend/logout.php
 * Endpoint: POST /backend/logout.php
 * Menghapus session dan mengeluarkan pengguna.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_ORIGIN']) ? htmlspecialchars($_SERVER['HTTP_ORIGIN'], ENT_QUOTES) : '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

init_session();
require_method('POST');

// Hapus semua data session
$_SESSION = [];

// Hapus cookie session
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

json_response(['success' => true, 'message' => 'Anda telah berhasil keluar.']);
