<?php
declare(strict_types=1);

$page_title  = 'Manajemen Pengguna';
$active_menu = 'users';

require_once __DIR__ . '/_guard.php';
require_admin();
require_once __DIR__ . '/_layout.php';
?>

<!-- MAIN CONTENT -->
<main class="main-wrap">
    <div class="page-header">
        <div class="breadcrumb"><a href="dashboard.php">Dashboard</a> / Pengguna</div>
        <h2><i class="fas fa-users" style="color:var(--secondary)"></i> Manajemen Pengguna</h2>
        <p>Kelola akun pengguna: tambah, edit, dan hapus.</p>
    </div>

    <!-- Alert area -->
    <div id="page-alert" style="display:none"></div>

    <!-- Toolbar -->
    <div class="card" style="padding:16px 20px">
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
            <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <input type="text" id="search-input" placeholder="Cari nama, username, email..." style="padding:8px 12px; border:1.5px solid var(--border); border-radius:7px; font-family:'Source Sans 3',sans-serif; font-size:.875rem; width:260px; outline:none;">
                <select id="role-filter" style="padding:8px 12px; border:1.5px solid var(--border); border-radius:7px; font-family:'Source Sans 3',sans-serif; font-size:.875rem; outline:none; background:#fafafa;">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="operator">Operator</option>
                    <option value="guru">Guru</option>
                    <option value="siswa">Siswa</option>
                </select>
                <button class="btn btn-outline btn-sm" id="btn-search"><i class="fas fa-search"></i> Cari</button>
            </div>
            <button class="btn btn-primary" id="btn-add-user">
                <i class="fas fa-user-plus"></i> Tambah Pengguna
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="card" style="padding:0">
        <div class="table-wrap">
            <table id="users-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Login Terakhir</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="users-tbody">
                    <tr><td colspan="8" style="text-align:center;padding:30px;color:var(--text-muted)"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- ── MODAL Tambah / Edit ──────────────────────────────── -->
<div class="modal-overlay" id="modal-user">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="modal-title">Tambah Pengguna</span>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modal-alert" style="display:none"></div>
        <form id="user-form" novalidate>
            <input type="hidden" id="edit-id" value="">
            <div class="form-row">
                <div class="form-group">
                    <label for="f-fullname">Nama Lengkap <span style="color:red">*</span></label>
                    <input type="text" id="f-fullname" placeholder="Nama lengkap" required>
                </div>
                <div class="form-group">
                    <label for="f-username">Username <span style="color:red">*</span></label>
                    <input type="text" id="f-username" placeholder="Username unik" required autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label for="f-email">Email <span style="color:red">*</span></label>
                <input type="email" id="f-email" placeholder="email@contoh.com" required autocomplete="off">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="f-role">Role <span style="color:red">*</span></label>
                    <select id="f-role">
                        <option value="siswa">Siswa</option>
                        <option value="guru">Guru</option>
                        <option value="operator">Operator</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="f-status">Status</label>
                    <select id="f-status">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="f-password">Password <span id="pw-required" style="color:red">*</span></label>
                <input type="password" id="f-password" placeholder="Min. 6 karakter" autocomplete="new-password">
                <div class="form-hint" id="pw-hint">Minimal 6 karakter.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-save">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ── MODAL Konfirmasi Hapus ────────────────────────────── -->
<div class="modal-overlay" id="modal-delete">
    <div class="modal" style="max-width:420px">
        <div class="modal-header">
            <span class="modal-title">Konfirmasi Hapus</span>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <p style="margin-bottom:6px">Anda akan menghapus pengguna:</p>
        <p><strong id="delete-name"></strong></p>
        <p style="color:var(--danger); font-size:.875rem; margin-top:8px">
            <i class="fas fa-exclamation-triangle"></i> Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeDeleteModal()">Batal</button>
            <button class="btn btn-danger" id="btn-confirm-delete">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    </div>
</div>

<script>
const API = '../backend/users.php';
let deleteId = 0;

// ── Helpers ────────────────────────────────────────────────
function showPageAlert(msg, type) {
    const el = document.getElementById('page-alert');
    el.innerHTML = `<div class="alert alert-${type}"><i class="fas fa-${type==='success'?'check-circle':'exclamation-circle'}"></i>${msg}</div>`;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 4000);
}

function showModalAlert(msg, type) {
    const el = document.getElementById('modal-alert');
    el.innerHTML = `<div class="alert alert-${type}"><i class="fas fa-exclamation-circle"></i>${msg}</div>`;
    el.style.display = 'block';
}

function roleBadge(role) {
    return `<span class="badge badge-${role}">${role.charAt(0).toUpperCase()+role.slice(1)}</span>`;
}

function statusBadge(active) {
    return active
        ? '<span class="badge badge-active"><i class="fas fa-check-circle"></i> Aktif</span>'
        : '<span class="badge badge-inactive"><i class="fas fa-times-circle"></i> Nonaktif</span>';
}

// ── Load Users ──────────────────────────────────────────────
async function loadUsers() {
    const search = document.getElementById('search-input').value.trim();
    const role   = document.getElementById('role-filter').value;
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    if (role)   params.set('role', role);

    const tbody = document.getElementById('users-tbody');
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:var(--text-muted)"><i class="fas fa-spinner fa-spin"></i> Memuat...</td></tr>';

    try {
        const res  = await fetch(API + (params.toString() ? '?' + params : ''), { credentials: 'include' });
        const data = await res.json();

        if (!data.success) { tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:30px;color:var(--danger)">${data.message}</td></tr>`; return; }

        if (!data.data.length) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:var(--text-muted)">Tidak ada data pengguna.</td></tr>';
            return;
        }

        tbody.innerHTML = data.data.map((u, i) => `
            <tr>
                <td style="color:var(--text-muted)">${i + 1}</td>
                <td><strong>${escHtml(u.full_name)}</strong></td>
                <td><code style="font-size:.85rem">${escHtml(u.username)}</code></td>
                <td style="font-size:.875rem">${escHtml(u.email)}</td>
                <td>${roleBadge(u.role)}</td>
                <td>${statusBadge(u.is_active == 1)}</td>
                <td style="font-size:.8rem;color:var(--text-muted)">${u.last_login ?? '—'}</td>
                <td style="text-align:center;white-space:nowrap">
                    <button class="btn btn-warning btn-sm" onclick="openEdit(${u.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm" onclick="openDelete(${u.id}, '${escHtml(u.full_name)}')"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`
        ).join('');
    } catch {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:var(--danger)">Gagal menghubungi server.</td></tr>';
    }
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Modal Tambah ────────────────────────────────────────────
document.getElementById('btn-add-user').addEventListener('click', () => {
    document.getElementById('modal-title').textContent = 'Tambah Pengguna';
    document.getElementById('user-form').reset();
    document.getElementById('edit-id').value = '';
    document.getElementById('modal-alert').style.display = 'none';
    document.getElementById('pw-required').style.display = 'inline';
    document.getElementById('pw-hint').textContent = 'Minimal 6 karakter.';
    document.getElementById('modal-user').classList.add('open');
});

// ── Modal Edit ──────────────────────────────────────────────
async function openEdit(id) {
    document.getElementById('modal-alert').style.display = 'none';
    document.getElementById('modal-title').textContent = 'Edit Pengguna';
    document.getElementById('pw-required').style.display = 'none';
    document.getElementById('pw-hint').textContent = 'Kosongkan jika tidak ingin mengganti password.';

    try {
        const res  = await fetch(`${API}?id=${id}`, { credentials: 'include' });
        // Ambil dari daftar karena tidak ada endpoint get-by-id
        const res2 = await fetch(API, { credentials: 'include' });
        const data = await res2.json();
        const u    = data.data.find(x => x.id == id);
        if (!u) return showPageAlert('Data pengguna tidak ditemukan.', 'error');

        document.getElementById('edit-id').value   = u.id;
        document.getElementById('f-fullname').value = u.full_name;
        document.getElementById('f-username').value  = u.username;
        document.getElementById('f-email').value     = u.email;
        document.getElementById('f-role').value      = u.role;
        document.getElementById('f-status').value    = u.is_active;
        document.getElementById('f-password').value  = '';
        document.getElementById('modal-user').classList.add('open');
    } catch {
        showPageAlert('Gagal memuat data pengguna.', 'error');
    }
}

function closeModal() { document.getElementById('modal-user').classList.remove('open'); }

// ── Form Submit (Tambah / Edit) ─────────────────────────────
document.getElementById('user-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    document.getElementById('modal-alert').style.display = 'none';

    const editId   = document.getElementById('edit-id').value;
    const isEdit   = editId !== '';
    const payload  = {
        full_name: document.getElementById('f-fullname').value.trim(),
        username:  document.getElementById('f-username').value.trim(),
        email:     document.getElementById('f-email').value.trim(),
        role:      document.getElementById('f-role').value,
        is_active: parseInt(document.getElementById('f-status').value),
        password:  document.getElementById('f-password').value,
    };

    const btn = document.getElementById('btn-save');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const url = isEdit ? `${API}?id=${editId}` : API;
        const res  = await fetch(url, {
            method:      isEdit ? 'PUT' : 'POST',
            credentials: 'include',
            headers:     { 'Content-Type': 'application/json' },
            body:        JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
            closeModal();
            showPageAlert(data.message, 'success');
            loadUsers();
        } else {
            showModalAlert(data.message, 'error');
        }
    } catch {
        showModalAlert('Gagal menghubungi server.', 'error');
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
    }
});

// ── Modal Hapus ─────────────────────────────────────────────
function openDelete(id, name) {
    deleteId = id;
    document.getElementById('delete-name').textContent = name;
    document.getElementById('modal-delete').classList.add('open');
}
function closeDeleteModal() { document.getElementById('modal-delete').classList.remove('open'); }

document.getElementById('btn-confirm-delete').addEventListener('click', async () => {
    const btn = document.getElementById('btn-confirm-delete');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
        const res  = await fetch(`${API}?id=${deleteId}`, { method: 'DELETE', credentials: 'include' });
        const data = await res.json();
        closeDeleteModal();
        showPageAlert(data.message, data.success ? 'success' : 'error');
        if (data.success) loadUsers();
    } catch {
        showPageAlert('Gagal menghubungi server.', 'error');
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-trash"></i> Hapus';
    }
});

// Tutup modal saat klik overlay
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
});

// Cari
document.getElementById('btn-search').addEventListener('click', loadUsers);
document.getElementById('search-input').addEventListener('keydown', e => { if (e.key === 'Enter') loadUsers(); });
document.getElementById('role-filter').addEventListener('change', loadUsers);

// Buka modal tambah jika URL ?action=add
if (new URLSearchParams(location.search).get('action') === 'add') {
    document.getElementById('btn-add-user').click();
}

// Load awal
loadUsers();
</script>

</body>
</html>
