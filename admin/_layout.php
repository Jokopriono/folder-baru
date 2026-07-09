<?php
/**
 * admin/_layout.php
 * Komponen layout admin: header, sidebar, footer HTML.
 * Dipakai bersama oleh semua halaman admin.
 * 
 * Variabel yang diharapkan sebelum include:
 *   $page_title  — judul tab browser
 *   $active_menu — string: 'dashboard' | 'users' | 'change-password'
 */

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Admin', ENT_QUOTES) ?> | MTs Muhammadiyah Bireuen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:    #1a5a3d;
            --primary-dk: #144a31;
            --secondary:  #f2be42;
            --sidebar-w:  240px;
            --header-h:   60px;
            --text:       #333;
            --text-muted: #666;
            --border:     #e0e0e0;
            --bg:         #f5f7fa;
            --card-bg:    #fff;
            --danger:     #e74c3c;
            --success:    #27ae60;
            --warning:    #f39c12;
            --info:       #3498db;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Source Sans 3', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; flex-direction: column; }

        /* ── Topbar ─────────────────────────────────────────── */
        .topbar {
            position: fixed; top: 0; left: 0; right: 0; height: var(--header-h);
            background: var(--primary); color: #fff;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 20px; z-index: 100; box-shadow: 0 2px 6px rgba(0,0,0,.2);
        }
        .topbar-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: #fff; }
        .topbar-brand img { height: 36px; width: 36px; object-fit: contain; }
        .topbar-brand h1 { font-family: 'Oswald', sans-serif; font-size: 1.1rem; font-weight: 700; }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .topbar-user { font-size: .9rem; }
        .topbar-user span { font-weight: 600; }
        .btn-logout {
            background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3);
            color: #fff; padding: 6px 14px; border-radius: 6px;
            font-family: 'Source Sans 3', sans-serif; font-size: .85rem; cursor: pointer;
            transition: background .2s;
        }
        .btn-logout:hover { background: rgba(255,255,255,.25); }
        .sidebar-toggle { display: none; background: none; border: none; color: #fff; font-size: 1.2rem; cursor: pointer; }

        /* ── Sidebar ────────────────────────────────────────── */
        .sidebar {
            position: fixed; top: var(--header-h); left: 0; bottom: 0;
            width: var(--sidebar-w); background: #fff;
            border-right: 1px solid var(--border); overflow-y: auto;
            transition: transform .25s; z-index: 90;
        }
        .sidebar nav { padding: 16px 0; }
        .sidebar-section { padding: 8px 20px 4px; font-size: .75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; }
        .sidebar a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 20px; color: var(--text); text-decoration: none;
            font-size: .925rem; transition: background .15s, color .15s; border-left: 3px solid transparent;
        }
        .sidebar a:hover { background: #f0f7f3; color: var(--primary); border-left-color: var(--primary); }
        .sidebar a.active { background: #e8f5ee; color: var(--primary); font-weight: 600; border-left-color: var(--primary); }
        .sidebar a i { width: 18px; text-align: center; color: var(--text-muted); }
        .sidebar a.active i, .sidebar a:hover i { color: var(--primary); }
        .sidebar-divider { height: 1px; background: var(--border); margin: 8px 20px; }

        /* ── Main Content ───────────────────────────────────── */
        .main-wrap { margin-left: var(--sidebar-w); margin-top: var(--header-h); flex: 1; padding: 28px 28px 40px; min-height: calc(100vh - var(--header-h)); }
        .page-header { margin-bottom: 24px; }
        .page-header h2 { font-family: 'Oswald', sans-serif; font-size: 1.6rem; color: var(--primary); }
        .page-header p { color: var(--text-muted); font-size: .9rem; margin-top: 2px; }
        .breadcrumb { font-size: .85rem; color: var(--text-muted); margin-bottom: 6px; }
        .breadcrumb a { color: var(--primary); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        /* ── Cards ──────────────────────────────────────────── */
        .card { background: var(--card-bg); border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,.07); padding: 24px; margin-bottom: 24px; }
        .card-title { font-family: 'Oswald', sans-serif; font-size: 1.1rem; color: var(--primary); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .card-title i { color: var(--secondary); }

        /* ── Stat Cards ─────────────────────────────────────── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 18px; margin-bottom: 24px; }
        .stat-card { background: var(--card-bg); border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,.07); padding: 20px; display: flex; align-items: center; gap: 16px; }
        .stat-icon { width: 52px; height: 52px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
        .stat-icon.green  { background: #e8f5ee; color: var(--primary); }
        .stat-icon.blue   { background: #eaf3fb; color: var(--info); }
        .stat-icon.orange { background: #fff5e6; color: var(--warning); }
        .stat-icon.red    { background: #fdecea; color: var(--danger); }
        .stat-info { min-width: 0; }
        .stat-number { font-family: 'Oswald', sans-serif; font-size: 1.8rem; color: var(--text); line-height: 1; }
        .stat-label  { font-size: .82rem; color: var(--text-muted); margin-top: 2px; }

        /* ── Tables ─────────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .9rem; }
        thead th { background: #f5f7fa; color: var(--text-muted); font-weight: 700; font-size: .8rem; text-transform: uppercase; letter-spacing: .4px; padding: 10px 14px; text-align: left; border-bottom: 2px solid var(--border); }
        tbody td { padding: 11px 14px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #fafcfa; }

        /* ── Badges ─────────────────────────────────────────── */
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: .78rem; font-weight: 600; }
        .badge-admin    { background: #e8f5ee; color: var(--primary); }
        .badge-guru     { background: #eaf3fb; color: var(--info); }
        .badge-siswa    { background: #f5f0ff; color: #7b5ea7; }
        .badge-operator { background: #fff5e6; color: var(--warning); }
        .badge-active   { background: #eafaf1; color: var(--success); }
        .badge-inactive { background: #fdecea; color: var(--danger); }

        /* ── Buttons ─────────────────────────────────────────── */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 7px; font-family: 'Source Sans 3', sans-serif; font-size: .875rem; font-weight: 600; cursor: pointer; border: none; transition: opacity .2s, transform .1s; text-decoration: none; }
        .btn:active { transform: scale(.98); }
        .btn-primary   { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dk); color: #fff; }
        .btn-danger    { background: var(--danger); color: #fff; }
        .btn-danger:hover { opacity: .88; }
        .btn-warning   { background: var(--warning); color: #fff; }
        .btn-warning:hover { opacity: .88; }
        .btn-sm { padding: 5px 10px; font-size: .8rem; }
        .btn-outline { background: #fff; border: 1.5px solid var(--border); color: var(--text); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }

        /* ── Forms ──────────────────────────────────────────── */
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-weight: 600; font-size: .875rem; margin-bottom: 5px; }
        .form-group input, .form-group select {
            width: 100%; padding: 9px 12px; border: 1.5px solid var(--border); border-radius: 7px;
            font-family: 'Source Sans 3', sans-serif; font-size: .9rem; color: var(--text);
            background: #fafafa; transition: border-color .2s, box-shadow .2s; outline: none;
        }
        .form-group input:focus, .form-group select:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(26,90,61,.1); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-hint { font-size: .8rem; color: var(--text-muted); margin-top: 4px; }

        /* ── Alerts ─────────────────────────────────────────── */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: .875rem; margin-bottom: 18px; display: flex; align-items: flex-start; gap: 10px; }
        .alert i { margin-top: 2px; flex-shrink: 0; }
        .alert-success { background: #eafaf1; color: var(--success); border: 1px solid #a9dfbf; }
        .alert-error   { background: #fdecea; color: var(--danger);  border: 1px solid #f5c6cb; }
        .alert-info    { background: #eaf3fb; color: var(--info);    border: 1px solid #bee3f8; }

        /* ── Modal ──────────────────────────────────────────── */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 200; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 12px; padding: 28px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 8px 30px rgba(0,0,0,.2); animation: fadeIn .2s; }
        @keyframes fadeIn { from { opacity:0; transform: translateY(-10px); } to { opacity:1; transform: none; } }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-title { font-family: 'Oswald', sans-serif; font-size: 1.2rem; color: var(--primary); }
        .modal-close { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-muted); padding: 4px; }
        .modal-close:hover { color: var(--danger); }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }

        /* ── Responsive ─────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar-toggle { display: block; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); box-shadow: 4px 0 20px rgba(0,0,0,.15); }
            .main-wrap { margin-left: 0; padding: 18px; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
    <a href="dashboard.php" class="topbar-brand">
        <img src="../Image/logo%20mts.png" alt="Logo">
        <h1>MTs Admin Panel</h1>
    </a>
    <div class="topbar-right">
        <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <span class="topbar-user">Halo, <span><?= htmlspecialchars($current_user['full_name'], ENT_QUOTES) ?></span></span>
        <button class="btn-logout" id="btn-logout">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </button>
    </div>
</header>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <nav>
        <div class="sidebar-section">Menu Utama</div>
        <a href="dashboard.php" class="<?= ($active_menu ?? '') === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        <div class="sidebar-section">Manajemen</div>
        <a href="users.php" class="<?= ($active_menu ?? '') === 'users' ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Pengguna
        </a>
        <a href="news.php" class="<?= ($active_menu ?? '') === 'news' ? 'active' : '' ?>">
            <i class="fas fa-newspaper"></i> Berita
        </a>
        <a href="content.php" class="<?= ($active_menu ?? '') === 'content' ? 'active' : '' ?>">
            <i class="fas fa-edit"></i> Konten Website
        </a>

        <div class="sidebar-divider"></div>
        <div class="sidebar-section">Akun Saya</div>
        <a href="change-password.php" class="<?= ($active_menu ?? '') === 'change-password' ? 'active' : '' ?>">
            <i class="fas fa-key"></i> Ganti Password
        </a>
        <a href="../index.html">
            <i class="fas fa-home"></i> Kembali ke Website
        </a>
    </nav>
</aside>

<!-- Sidebar overlay (mobile) -->
<div id="sidebar-overlay" style="display:none;position:fixed;inset:0;z-index:89;background:rgba(0,0,0,.3)" onclick="closeSidebar()"></div>

<script>
    // Sidebar toggle
    document.getElementById('sidebar-toggle').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebar-overlay').style.display =
            document.getElementById('sidebar').classList.contains('open') ? 'block' : 'none';
    });
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').style.display = 'none';
    }

    // Logout
    document.getElementById('btn-logout').addEventListener('click', async () => {
        if (!confirm('Yakin ingin keluar?')) return;
        await fetch('../backend/logout.php', { method: 'POST', credentials: 'include' });
        window.location.href = '../login.html';
    });
</script>
