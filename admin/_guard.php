<?php
/**
 * admin/_guard.php
 * Sertakan file ini di AWAL setiap halaman admin.
 * Akan redirect ke halaman login jika sesi tidak valid.
 */

declare(strict_types=1);

require_once __DIR__ . '/../backend/config.php';

init_session();

// Cek login
if (empty($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit;
}

// Cek session timeout
if ((time() - (int)($_SESSION['logged_in_at'] ?? 0)) > SESSION_LIFETIME) {
    session_destroy();
    header('Location: ../login.html?timeout=1');
    exit;
}

// Fungsi helper: hanya admin & operator yang boleh akses
function require_admin(): void {
    if (!in_array($_SESSION['role'] ?? '', ['admin', 'operator'], true)) {
        http_response_code(403);
        echo '<p style="font-family:sans-serif;padding:40px">Akses ditolak. Anda tidak memiliki izin.</p>';
        exit;
    }
}

// Data user yang sedang login (shortcut)
$current_user = [
    'id'        => $_SESSION['user_id'],
    'username'  => $_SESSION['username'],
    'full_name' => $_SESSION['full_name'],
    'role'      => $_SESSION['role'],
];
