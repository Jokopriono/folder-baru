<?php
/**
 * backend/setup.php
 * ─────────────────────────────────────────────────────────────
 *  Script setup SATU KALI untuk membuat akun admin pertama.
 *  PENTING: HAPUS file ini setelah dijalankan!
 *
 *  Langkah:
 *    1. Pastikan database & tabel sudah dibuat (jalankan database/schema.sql)
 *    2. Buka di browser: http://localhost/backend/setup.php
 *    3. Catat kredensial yang ditampilkan
 *    4. SEGERA hapus file ini dari server
 * ─────────────────────────────────────────────────────────────
 */

declare(strict_types=1);

// Keamanan: hanya bisa diakses dari localhost
$allowed_ips = ['127.0.0.1', '::1'];
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', $allowed_ips, true)) {
    http_response_code(403);
    die('Akses ditolak.');
}

require_once __DIR__ . '/config.php';

// ----------------------------------------------------------------
// Kredensial admin default — ganti sesuai kebutuhan
// ----------------------------------------------------------------
$admin = [
    'username'  => 'admin',
    'email'     => 'admin@mtsbireuen.sch.id',
    'full_name' => 'Administrator',
    'password'  => 'Admin@1234',   // GANTI PASSWORD INI setelah login pertama!
    'role'      => 'admin',
];

$hash = password_hash($admin['password'], PASSWORD_BCRYPT, ['cost' => 12]);

try {
    $pdo  = get_db();
    $stmt = $pdo->prepare(
        'INSERT INTO users (username, email, full_name, password_hash, role, is_active)
         VALUES (:username, :email, :full_name, :hash, :role, 1)
         ON DUPLICATE KEY UPDATE
             password_hash = VALUES(password_hash),
             updated_at    = CURRENT_TIMESTAMP'
    );
    $stmt->execute([
        ':username'  => $admin['username'],
        ':email'     => $admin['email'],
        ':full_name' => $admin['full_name'],
        ':hash'      => $hash,
        ':role'      => $admin['role'],
    ]);
    $status = 'berhasil';
} catch (PDOException $e) {
    $status = 'GAGAL: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Setup Akun Admin</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 60px auto; padding: 20px; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 24px; }
        .ok  { color: #27ae60; } .err { color: #e74c3c; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 4px; }
        .warn { background: #fff3cd; border: 1px solid #ffc107; padding: 12px; border-radius: 6px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Setup Akun Admin MTs Bireuen</h2>
        <p>Status: <strong class="<?= $status === 'berhasil' ? 'ok' : 'err' ?>"><?= $status ?></strong></p>

        <?php if ($status === 'berhasil'): ?>
        <table style="border-collapse:collapse; width:100%; margin-top:16px;">
            <tr><td style="padding:6px;color:#666">Username</td><td><code><?= htmlspecialchars($admin['username'], ENT_QUOTES) ?></code></td></tr>
            <tr><td style="padding:6px;color:#666">Email</td><td><code><?= htmlspecialchars($admin['email'], ENT_QUOTES) ?></code></td></tr>
            <tr><td style="padding:6px;color:#666">Password</td><td><code><?= htmlspecialchars($admin['password'], ENT_QUOTES) ?></code></td></tr>
            <tr><td style="padding:6px;color:#666">Role</td><td><code><?= htmlspecialchars($admin['role'], ENT_QUOTES) ?></code></td></tr>
        </table>
        <div class="warn">
            <strong>&#9888; Penting:</strong>
            <ol style="margin:8px 0 0; padding-left:20px">
                <li>Segera <strong>hapus file <code>backend/setup.php</code></strong> dari server.</li>
                <li>Ganti password admin setelah login pertama.</li>
            </ol>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
