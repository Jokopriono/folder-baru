<?php
declare(strict_types=1);
$page_title  = 'Kelola Berita';
$active_menu = 'news';
require_once __DIR__ . '/_guard.php';
require_admin();
require_once __DIR__ . '/_layout.php';
?>
<main class="main-wrap">
    <div class="page-header">
        <div class="breadcrumb"><a href="dashboard.php">Dashboard</a> / Berita</div>
        <h2><i class="fas fa-newspaper" style="color:var(--secondary)"></i> Kelola Berita</h2>
        <p>Tambah, edit, dan hapus artikel berita yang tampil di website.</p>
    </div>

    <div id="page-alert" style="display:none"></div>

    <!-- Toolbar -->
    <div class="card" style="padding:16px 20px;margin-bottom:0">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
            <input type="text" id="search-input" placeholder="Cari judul berita..." style="padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-family:'Source Sans 3',sans-serif;font-size:.875rem;width:280px;outline:none;">
            <button class="btn btn-primary" id="btn-add"><i class="fas fa-plus"></i> Tambah Berita</button>
        </div>
    </div>

    <!-- Table -->
    <div class="card" style="padding:0">
        <div class="table-wrap">
            <table>
                <thead><tr><th>#</th><th>Judul</th><th>Ringkasan</th><th>Status</th><th>Tanggal</th><th style="text-align:center">Aksi</th></tr></thead>
                <tbody id="news-tbody">
                    <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--text-muted)"><i class="fas fa-spinner fa-spin"></i> Memuat...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="pagination" style="padding:14px 20px;display:flex;gap:8px;align-items:center;justify-content:space-between;flex-wrap:wrap;border-top:1px solid var(--border)">
            <span id="total-info" style="font-size:.85rem;color:var(--text-muted)"></span>
            <div style="display:flex;gap:6px">
                <button class="btn btn-outline btn-sm" id="btn-prev" disabled><i class="fas fa-chevron-left"></i> Prev</button>
                <button class="btn btn-outline btn-sm" id="btn-next" disabled>Next <i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</main>

<!-- MODAL Tambah/Edit -->
<div class="modal-overlay" id="modal-news" style="align-items:flex-start;padding:40px 15px;overflow-y:auto">
    <div class="modal" style="max-width:680px;width:100%">
        <div class="modal-header">
            <span class="modal-title" id="modal-title">Tambah Berita</span>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modal-alert" style="display:none"></div>
        <form id="news-form" novalidate>
            <input type="hidden" id="edit-id">
            <div class="form-group">
                <label for="f-title">Judul Berita <span style="color:red">*</span></label>
                <input type="text" id="f-title" placeholder="Masukkan judul berita" required>
            </div>
            <div class="form-group">
                <label for="f-summary">Ringkasan <span style="color:red">*</span></label>
                <textarea id="f-summary" rows="3" placeholder="Ringkasan singkat yang tampil di halaman daftar berita..." required style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:7px;font-family:'Source Sans 3',sans-serif;font-size:.9rem;resize:vertical;outline:none;background:#fafafa"></textarea>
            </div>
            <div class="form-group">
                <label for="f-body">Isi Berita <span style="color:red">*</span></label>
                <textarea id="f-body" rows="10" placeholder="Tulis isi berita lengkap di sini..." required style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:7px;font-family:'Source Sans 3',sans-serif;font-size:.9rem;resize:vertical;outline:none;background:#fafafa"></textarea>
                <div class="form-hint">Mendukung tag HTML dasar: &lt;b&gt;, &lt;i&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;</div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="f-image">URL Gambar</label>
                    <input type="text" id="f-image" placeholder="https://... atau Image/nama-file.jpg">
                    <div class="form-hint">Kosongkan jika tidak ada gambar.</div>
                </div>
                <div class="form-group">
                    <label for="f-status">Status</label>
                    <select id="f-status">
                        <option value="1">Dipublikasikan</option>
                        <option value="0">Draft (Tersembunyi)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-save"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL Hapus -->
<div class="modal-overlay" id="modal-delete">
    <div class="modal" style="max-width:420px">
        <div class="modal-header">
            <span class="modal-title">Konfirmasi Hapus</span>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <p>Anda akan menghapus berita:</p>
        <p style="margin-top:6px"><strong id="delete-title"></strong></p>
        <p style="color:var(--danger);font-size:.875rem;margin-top:8px"><i class="fas fa-exclamation-triangle"></i> Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeDeleteModal()">Batal</button>
            <button class="btn btn-danger" id="btn-confirm-delete"><i class="fas fa-trash"></i> Hapus</button>
        </div>
    </div>
</div>

<script>
const API = '../backend/news.php';
const LIMIT = 8;
let currentOffset = 0, deleteId = 0, searchQuery = '';

function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function showPageAlert(msg, type) {
    const el = document.getElementById('page-alert');
    el.innerHTML = `<div class="alert alert-${type}"><i class="fas fa-${type==='success'?'check-circle':'exclamation-circle'}"></i>${msg}</div>`;
    el.style.display = 'block';
    setTimeout(() => el.style.display='none', 4000);
}
function showModalAlert(msg, type) {
    const el = document.getElementById('modal-alert');
    el.innerHTML = `<div class="alert alert-${type}"><i class="fas fa-exclamation-circle"></i>${msg}</div>`;
    el.style.display = 'block';
}

async function loadNews() {
    const tbody = document.getElementById('news-tbody');
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:var(--text-muted)"><i class="fas fa-spinner fa-spin"></i> Memuat...</td></tr>';
    try {
        const url = `${API}?limit=${LIMIT}&offset=${currentOffset}${searchQuery ? '&search='+encodeURIComponent(searchQuery) : ''}`;
        const res  = await fetch(url, { credentials: 'include' });
        const data = await res.json();
        if (!data.success) { tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:30px;color:var(--danger)">${data.message}</td></tr>`; return; }
        if (!data.data.length) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:var(--text-muted)">Belum ada berita.</td></tr>'; }
        else {
            tbody.innerHTML = data.data.map((n, i) => `
                <tr>
                    <td style="color:var(--text-muted)">${currentOffset+i+1}</td>
                    <td><strong style="font-size:.9rem">${escHtml(n.title)}</strong></td>
                    <td style="font-size:.85rem;color:var(--text-muted);max-width:240px">${escHtml(n.summary).substring(0,80)}...</td>
                    <td>${n.is_published=='1'
                        ? '<span class="badge badge-active"><i class="fas fa-check-circle"></i> Publik</span>'
                        : '<span class="badge badge-inactive">Draft</span>'}</td>
                    <td style="font-size:.82rem;color:var(--text-muted)">${(n.published_at||'').substring(0,10)}</td>
                    <td style="text-align:center;white-space:nowrap">
                        <button class="btn btn-warning btn-sm" onclick="openEdit(${n.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger btn-sm" onclick="openDelete(${n.id},'${escHtml(n.title)}')"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>`).join('');
        }
        const total = data.total;
        document.getElementById('total-info').textContent = `Total: ${total} berita`;
        document.getElementById('btn-prev').disabled = currentOffset <= 0;
        document.getElementById('btn-next').disabled = currentOffset + LIMIT >= total;
    } catch { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:var(--danger)">Gagal menghubungi server.</td></tr>'; }
}

// Tambah
document.getElementById('btn-add').addEventListener('click', () => {
    document.getElementById('modal-title').textContent = 'Tambah Berita';
    document.getElementById('news-form').reset();
    document.getElementById('edit-id').value = '';
    document.getElementById('modal-alert').style.display = 'none';
    document.getElementById('modal-news').classList.add('open');
});

// Edit
async function openEdit(id) {
    document.getElementById('modal-alert').style.display = 'none';
    document.getElementById('modal-title').textContent = 'Edit Berita';
    try {
        const res  = await fetch(`${API}?id=${id}`, { credentials: 'include' });
        const data = await res.json();
        if (!data.success) return showPageAlert(data.message, 'error');
        const n = data.data;
        document.getElementById('edit-id').value    = n.id;
        document.getElementById('f-title').value    = n.title;
        document.getElementById('f-summary').value  = n.summary;
        document.getElementById('f-body').value     = n.body;
        document.getElementById('f-image').value    = n.image_url || '';
        document.getElementById('f-status').value   = n.is_published;
        document.getElementById('modal-news').classList.add('open');
    } catch { showPageAlert('Gagal memuat data.', 'error'); }
}

function closeModal() { document.getElementById('modal-news').classList.remove('open'); }

// Submit
document.getElementById('news-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    document.getElementById('modal-alert').style.display = 'none';
    const editId = document.getElementById('edit-id').value;
    const payload = {
        title:        document.getElementById('f-title').value.trim(),
        summary:      document.getElementById('f-summary').value.trim(),
        body:         document.getElementById('f-body').value.trim(),
        image_url:    document.getElementById('f-image').value.trim(),
        is_published: parseInt(document.getElementById('f-status').value),
    };
    const btn = document.getElementById('btn-save');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    try {
        const res  = await fetch(editId ? `${API}?id=${editId}` : API, {
            method: editId ? 'PUT' : 'POST', credentials: 'include',
            headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.success) { closeModal(); showPageAlert(data.message, 'success'); loadNews(); }
        else showModalAlert(data.message, 'error');
    } catch { showModalAlert('Gagal menghubungi server.', 'error'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan'; }
});

// Hapus
function openDelete(id, title) { deleteId=id; document.getElementById('delete-title').textContent=title; document.getElementById('modal-delete').classList.add('open'); }
function closeDeleteModal() { document.getElementById('modal-delete').classList.remove('open'); }
document.getElementById('btn-confirm-delete').addEventListener('click', async () => {
    const btn = document.getElementById('btn-confirm-delete');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    try {
        const res  = await fetch(`${API}?id=${deleteId}`, { method:'DELETE', credentials:'include' });
        const data = await res.json();
        closeDeleteModal(); showPageAlert(data.message, data.success?'success':'error');
        if (data.success) loadNews();
    } catch { showPageAlert('Gagal.', 'error'); }
    finally { btn.disabled=false; btn.innerHTML='<i class="fas fa-trash"></i> Hapus'; }
});

// Pagination
document.getElementById('btn-prev').addEventListener('click', () => { currentOffset = Math.max(0, currentOffset-LIMIT); loadNews(); });
document.getElementById('btn-next').addEventListener('click', () => { currentOffset += LIMIT; loadNews(); });

// Search
document.getElementById('search-input').addEventListener('input', function() { searchQuery = this.value.trim(); currentOffset=0; clearTimeout(this._t); this._t = setTimeout(loadNews, 400); });

document.querySelectorAll('.modal-overlay').forEach(el => el.addEventListener('click', e => { if (e.target===el) el.classList.remove('open'); }));

loadNews();
</script>
</body></html>
