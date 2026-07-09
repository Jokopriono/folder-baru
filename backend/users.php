<?php
/**
 * backend/users.php
 * REST-style API untuk manajemen pengguna.
 * 
 * GET    /backend/users.php          — daftar semua user
 * POST   /backend/users.php          — tambah user baru
 * PUT    /backend/users.php?id=N     — update user
 * DELETE /backend/users.php?id=N     — hapus user
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_ORIGIN']) ? htmlspecialchars($_SERVER['HTTP_ORIGIN'], ENT_QUOTES) : '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

init_session();

// Hanya admin & operator yang boleh akses
if (empty($_SESSION['user_id'])) json_response(['success' => false, 'message' => 'Unauthorized.'], 401);
if (!in_array($_SESSION['role'] ?? '', ['admin', 'operator'], true))
    json_response(['success' => false, 'message' => 'Akses ditolak.'], 403);

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;

try {
    $pdo = get_db();

    // ── GET: Daftar semua user ──────────────────────────────
    if ($method === 'GET') {
        $search = trim($_GET['search'] ?? '');
        $role   = $_GET['role'] ?? '';

        $sql    = 'SELECT id, username, email, full_name, role, is_active, last_login, created_at FROM users WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (username LIKE :s OR email LIKE :s OR full_name LIKE :s)';
            $params[':s'] = '%' . $search . '%';
        }
        if (in_array($role, ['admin','guru','siswa','operator'], true)) {
            $sql .= ' AND role = :role';
            $params[':role'] = $role;
        }

        $sql .= ' ORDER BY created_at DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        json_response(['success' => true, 'data' => $stmt->fetchAll()]);
    }

    // ── POST: Tambah user baru ──────────────────────────────
    if ($method === 'POST') {
        $body = json_decode((string) file_get_contents('php://input'), true);

        $username  = trim((string)($body['username']  ?? ''));
        $email     = trim((string)($body['email']     ?? ''));
        $full_name = trim((string)($body['full_name'] ?? ''));
        $password  = (string)($body['password'] ?? '');
        $role      = (string)($body['role']      ?? 'siswa');
        $is_active = isset($body['is_active']) ? (int)(bool)$body['is_active'] : 1;

        // Validasi
        if (!$username || !$email || !$full_name || !$password)
            json_response(['success' => false, 'message' => 'Semua field wajib diisi.'], 422);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            json_response(['success' => false, 'message' => 'Format email tidak valid.'], 422);
        if (strlen($password) < 6)
            json_response(['success' => false, 'message' => 'Password minimal 6 karakter.'], 422);
        if (!in_array($role, ['admin','guru','siswa','operator'], true))
            json_response(['success' => false, 'message' => 'Role tidak valid.'], 422);

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare(
            'INSERT INTO users (username, email, full_name, password_hash, role, is_active)
             VALUES (:username, :email, :full_name, :hash, :role, :is_active)'
        );
        $stmt->execute([
            ':username'  => $username,
            ':email'     => $email,
            ':full_name' => $full_name,
            ':hash'      => $hash,
            ':role'      => $role,
            ':is_active' => $is_active,
        ]);

        json_response(['success' => true, 'message' => 'Pengguna berhasil ditambahkan.', 'id' => (int)$pdo->lastInsertId()]);
    }

    // ── PUT: Update user ────────────────────────────────────
    if ($method === 'PUT') {
        if ($id <= 0) json_response(['success' => false, 'message' => 'ID tidak valid.'], 422);

        $body = json_decode((string) file_get_contents('php://input'), true);

        $username  = trim((string)($body['username']  ?? ''));
        $email     = trim((string)($body['email']     ?? ''));
        $full_name = trim((string)($body['full_name'] ?? ''));
        $role      = (string)($body['role'] ?? '');
        $is_active = isset($body['is_active']) ? (int)(bool)$body['is_active'] : null;
        $password  = (string)($body['password'] ?? '');

        if (!$username || !$email || !$full_name)
            json_response(['success' => false, 'message' => 'Field username, email, dan nama lengkap wajib diisi.'], 422);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            json_response(['success' => false, 'message' => 'Format email tidak valid.'], 422);
        if (!in_array($role, ['admin','guru','siswa','operator'], true))
            json_response(['success' => false, 'message' => 'Role tidak valid.'], 422);

        // Cegah admin menghapus role diri sendiri
        if ($id === (int)$_SESSION['user_id'] && $role !== 'admin')
            json_response(['success' => false, 'message' => 'Anda tidak bisa mengubah role akun sendiri.'], 403);

        $params = [
            ':username'  => $username,
            ':email'     => $email,
            ':full_name' => $full_name,
            ':role'      => $role,
            ':is_active' => $is_active ?? 1,
            ':id'        => $id,
        ];

        $sql = 'UPDATE users SET username=:username, email=:email, full_name=:full_name, role=:role, is_active=:is_active';

        if ($password !== '') {
            if (strlen($password) < 6)
                json_response(['success' => false, 'message' => 'Password minimal 6 karakter.'], 422);
            $sql .= ', password_hash=:hash';
            $params[':hash'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        }

        $sql .= ' WHERE id=:id';
        $pdo->prepare($sql)->execute($params);

        json_response(['success' => true, 'message' => 'Data pengguna berhasil diperbarui.']);
    }

    // ── DELETE: Hapus user ──────────────────────────────────
    if ($method === 'DELETE') {
        if ($id <= 0) json_response(['success' => false, 'message' => 'ID tidak valid.'], 422);
        if ($id === (int)$_SESSION['user_id'])
            json_response(['success' => false, 'message' => 'Anda tidak bisa menghapus akun sendiri.'], 403);

        $pdo->prepare('DELETE FROM users WHERE id = :id')->execute([':id' => $id]);

        json_response(['success' => true, 'message' => 'Pengguna berhasil dihapus.']);
    }

    json_response(['success' => false, 'message' => 'Method tidak dikenali.'], 405);

} catch (PDOException $e) {
    // Periksa duplikat key
    if ($e->getCode() === '23000') {
        json_response(['success' => false, 'message' => 'Username atau email sudah digunakan.'], 409);
    }
    error_log('users.php DB error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Terjadi kesalahan server.'], 500);
}
