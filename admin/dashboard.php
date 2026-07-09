<?php
declare(strict_types=1);

$page_title  = 'Dashboard';
$active_menu = 'dashboard';

require_once __DIR__ . '/_guard.php';

// Ambil statistik dari database
$stats = ['total_users' => 0, 'admin' => 0, 'guru' => 0, 'siswa' => 0, 'operator' => 0, 'inactive' => 0];
$recent_logins = [];

try {
    $pdo = get_db();

    // Statistik jumlah user per role
    $rows = $pdo->query(
        'SELECT role, is_active, COUNT(*) AS total FROM users GROUP BY role, is_active'
    )->fetchAll();

    foreach ($rows as $row) {
        $stats['total_users'] += (int)$row['total'];
        if (isset($stats[$row['role']])) $stats[$row['role']] += (int)$row['total'];
        if (!(bool)$row['is_active']) $stats['inactive'] += (int)$row['total'];
    }

    // 5 login terakhir
    $recent_logins = $pdo->query(
        'SELECT full_name, username, role, last_login
         FROM users
         WHERE last_login IS NOT NULL
         ORDER BY last_login DESC
         LIMIT 5'
    )->fetchAll();

} catch (PDOException $e) {
    error_log('Dashboard DB error: ' . $e->getMessage());
}

require_once __DIR__ . '/_layout.php';
?>

<!-- MAIN CONTENT -->
<main class="main-wrap">
    <div class="page-header">
        <div class="breadcrumb">Admin Panel</div>
        <h2><i class="fas fa-th-large" style="color:var(--secondary)"></i> Dashboard</h2>
        <p>Selamat datang, <?= htmlspecialchars($current_user['full_name'], ENT_QUOTES) ?>. Berikut ringkasan data website.</p>
    </div>

    <!-- STAT CARDS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['total_users'] ?></div>
                <div class="stat-label">Total Pengguna</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['guru'] ?></div>
                <div class="stat-label">Guru</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['siswa'] ?></div>
                <div class="stat-label">Siswa</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-user-slash"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['inactive'] ?></div>
                <div class="stat-label">Akun Nonaktif</div>
            </div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; align-items:start;">

        <!-- Login Terakhir -->
        <div class="card">
            <div class="card-title"><i class="fas fa-history"></i> Login Terakhir</div>
            <?php if (empty($recent_logins)): ?>
                <p style="color:var(--text-muted); font-size:.9rem">Belum ada riwayat login.</p>
            <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Nama</th><th>Role</th><th>Waktu</th></tr></thead>
                    <tbody>
                    <?php foreach ($recent_logins as $u): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($u['full_name'], ENT_QUOTES) ?></strong><br>
                                <small style="color:var(--text-muted)"><?= htmlspecialchars($u['username'], ENT_QUOTES) ?></small>
                            </td>
                            <td><span class="badge badge-<?= htmlspecialchars($u['role'], ENT_QUOTES) ?>"><?= htmlspecialchars(ucfirst($u['role']), ENT_QUOTES) ?></span></td>
                            <td style="font-size:.82rem;color:var(--text-muted)"><?= htmlspecialchars($u['last_login'], ENT_QUOTES) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Menu Cepat -->
        <div class="card">
            <div class="card-title"><i class="fas fa-bolt"></i> Menu Cepat</div>
            <div style="display:flex; flex-direction:column; gap:10px;">
                <a href="users.php?action=add" class="btn btn-primary" style="justify-content:center">
                    <i class="fas fa-user-plus"></i> Tambah Pengguna Baru
                </a>
                <a href="users.php" class="btn btn-outline" style="justify-content:center">
                    <i class="fas fa-users"></i> Kelola Semua Pengguna
                </a>
                <a href="change-password.php" class="btn btn-outline" style="justify-content:center">
                    <i class="fas fa-key"></i> Ganti Password Saya
                </a>
                <a href="../index.html" class="btn btn-outline" style="justify-content:center" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Lihat Website
                </a>
            </div>
        </div>

    </div>
</main>

</body>
</html>
