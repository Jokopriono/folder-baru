<?php
declare(strict_types=1);
$page_title  = 'Pengaturan Konten';
$active_menu = 'content';
require_once __DIR__ . '/_guard.php';
require_admin();
require_once __DIR__ . '/_layout.php';
?>
<main class="main-wrap">
    <div class="page-header">
        <div class="breadcrumb"><a href="dashboard.php">Dashboard</a> / Konten Website</div>
        <h2><i class="fas fa-edit" style="color:var(--secondary)"></i> Pengaturan Konten Website</h2>
        <p>Ubah teks dan informasi yang tampil di halaman publik website.</p>
    </div>

    <div id="page-alert" style="display:none"></div>

    <!-- TICKER TEXT -->
    <div class="card">
        <div class="card-title"><i class="fas fa-bullhorn"></i> Teks Berjalan (Ticker)</div>
        <p style="font-size:.875rem;color:var(--text-muted);margin-bottom:18px">Teks yang tampil bergerak di bagian atas semua halaman website.</p>
        <form id="form-ticker">
            <div class="form-group">
                <label for="ticker_1">Teks 1 <span style="color:red">*</span></label>
                <input type="text" id="ticker_1" name="ticker_1" placeholder="Teks pertama">
            </div>
            <div class="form-group">
                <label for="ticker_2">Teks 2</label>
                <input type="text" id="ticker_2" name="ticker_2" placeholder="Teks kedua">
            </div>
            <div class="form-group">
                <label for="ticker_3">Teks 3</label>
                <input type="text" id="ticker_3" name="ticker_3" placeholder="Teks ketiga">
            </div>
            <button type="submit" class="btn btn-primary" id="btn-save-ticker">
                <i class="fas fa-save"></i> Simpan Ticker
            </button>
        </form>
    </div>

    <!-- HERO BANNER -->
    <div class="card">
        <div class="card-title"><i class="fas fa-image"></i> Hero Banner (Beranda)</div>
        <p style="font-size:.875rem;color:var(--text-muted);margin-bottom:18px">Teks besar yang tampil di atas gambar utama halaman beranda.</p>
        <form id="form-hero">
            <div class="form-group">
                <label for="hero_title">Judul Utama <span style="color:red">*</span></label>
                <input type="text" id="hero_title" name="hero_title" placeholder="Contoh: Mencerdaskan Bangsa">
            </div>
            <div class="form-group">
                <label for="hero_subtitle">Sub-judul</label>
                <input type="text" id="hero_subtitle" name="hero_subtitle" placeholder="Contoh: Membentuk Generasi Islami, Cerdas, Berkarakter">
            </div>
            <button type="submit" class="btn btn-primary" id="btn-save-hero">
                <i class="fas fa-save"></i> Simpan Hero Banner
            </button>
        </form>
    </div>

    <!-- SIDEBAR BERANDA -->
    <div class="card">
        <div class="card-title"><i class="fas fa-columns"></i> Sidebar Beranda</div>
        <p style="font-size:.875rem;color:var(--text-muted);margin-bottom:18px">Ubah teks singkat Tentang Kami dan info Kontak pada sidebar halaman beranda.</p>
        <form id="form-home-sidebar">
            <div class="form-group">
                <label for="about_text">Teks Tentang Kami</label>
                <textarea id="about_text" name="about_text" rows="4" placeholder="Deskripsi singkat sekolah pada widget Tentang Kami" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:7px;font-family:'Source Sans 3',sans-serif;font-size:.9rem;resize:vertical;outline:none;background:#fafafa"></textarea>
            </div>
            <div class="form-group">
                <label for="contact_phone">Nomor Telepon</label>
                <input type="text" id="contact_phone" name="contact_phone" placeholder="Contoh: +62 812 3456 7890">
            </div>
            <div class="form-group">
                <label for="contact_email">Email</label>
                <input type="text" id="contact_email" name="contact_email" placeholder="Contoh: admin@sekolah.sch.id">
            </div>
            <div class="form-group">
                <label for="contact_address">Alamat</label>
                <textarea id="contact_address" name="contact_address" rows="3" placeholder="Alamat sekolah" style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:7px;font-family:'Source Sans 3',sans-serif;font-size:.9rem;resize:vertical;outline:none;background:#fafafa"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" id="btn-save-sidebar">
                <i class="fas fa-save"></i> Simpan Sidebar Beranda
            </button>
        </form>
    </div>

    <!-- PENGUMUMAN BERANDA -->
    <div class="card">
        <div class="card-title"><i class="fas fa-bullhorn"></i> Pengumuman Beranda</div>
        <p style="font-size:.875rem;color:var(--text-muted);margin-bottom:18px">Ubah 3 item pengumuman yang tampil di sidebar beranda.</p>
        <form id="form-announcements">
            <div class="form-row">
                <div class="form-group">
                    <label for="announcement_1_title">Pengumuman 1 - Judul</label>
                    <input type="text" id="announcement_1_title" name="announcement_1_title" placeholder="Contoh: Jadwal Penilaian Akhir Semester">
                </div>
                <div class="form-group">
                    <label for="announcement_1_date">Pengumuman 1 - Tanggal</label>
                    <input type="text" id="announcement_1_date" name="announcement_1_date" placeholder="Contoh: 15 Juni 2026">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="announcement_2_title">Pengumuman 2 - Judul</label>
                    <input type="text" id="announcement_2_title" name="announcement_2_title" placeholder="Contoh: Undangan Pertemuan Wali Murid">
                </div>
                <div class="form-group">
                    <label for="announcement_2_date">Pengumuman 2 - Tanggal</label>
                    <input type="text" id="announcement_2_date" name="announcement_2_date" placeholder="Contoh: 13 Juni 2026">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="announcement_3_title">Pengumuman 3 - Judul</label>
                    <input type="text" id="announcement_3_title" name="announcement_3_title" placeholder="Contoh: PPDB 2026/2027 Resmi Dibuka">
                </div>
                <div class="form-group">
                    <label for="announcement_3_date">Pengumuman 3 - Tanggal</label>
                    <input type="text" id="announcement_3_date" name="announcement_3_date" placeholder="Contoh: 10 Juni 2026">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="btn-save-announcements">
                <i class="fas fa-save"></i> Simpan Pengumuman
            </button>
        </form>
    </div>

    <!-- PREVIEW -->
    <div class="card">
        <div class="card-title"><i class="fas fa-eye"></i> Preview Ticker</div>
        <div id="ticker-preview" style="background:var(--primary);color:#fff;padding:10px 20px;border-radius:8px;font-size:.875rem;display:flex;align-items:center;gap:12px;overflow:hidden">
            <span style="background:var(--secondary);color:#333;padding:3px 10px;border-radius:4px;font-weight:700;white-space:nowrap">SEKILAS INFO</span>
            <span id="preview-text" style="white-space:nowrap;animation:marquee 18s linear infinite">Memuat...</span>
        </div>
        <style>
            @keyframes marquee { 0%{transform:translateX(100%)} 100%{transform:translateX(-100%)} }
        </style>
    </div>
</main>

<script>
const API = '../backend/contents.php';

function showAlert(msg, type) {
    const el = document.getElementById('page-alert');
    el.innerHTML = `<div class="alert alert-${type}"><i class="fas fa-${type==='success'?'check-circle':'exclamation-circle'}"></i>${msg}</div>`;
    el.style.display = 'block';
    window.scrollTo({top:0,behavior:'smooth'});
    setTimeout(() => el.style.display='none', 4000);
}

// Muat data saat ini
async function loadContents() {
    try {
        const res  = await fetch(API, { credentials: 'include' });
        const data = await res.json();
        if (!data.success) return;
        const c = data.data;
        ['ticker_1','ticker_2','ticker_3','hero_title','hero_subtitle','about_text','contact_phone','contact_email','contact_address','announcement_1_title','announcement_1_date','announcement_2_title','announcement_2_date','announcement_3_title','announcement_3_date'].forEach(k => {
            const el = document.getElementById(k);
            if (el && c[k]) el.value = c[k].value;
        });
        updatePreview();
    } catch {}
}

function updatePreview() {
    const texts = [
        document.getElementById('ticker_1')?.value,
        document.getElementById('ticker_2')?.value,
        document.getElementById('ticker_3')?.value,
    ].filter(Boolean);
    document.getElementById('preview-text').textContent = texts.join('   |   ');
}

// Live preview saat mengetik
['ticker_1','ticker_2','ticker_3'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', updatePreview);
});

// Simpan Ticker
document.getElementById('form-ticker').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn-save-ticker');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    const payload = {
        ticker_1: document.getElementById('ticker_1').value.trim(),
        ticker_2: document.getElementById('ticker_2').value.trim(),
        ticker_3: document.getElementById('ticker_3').value.trim(),
    };
    try {
        const res  = await fetch(API, { method:'POST', credentials:'include', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
        const data = await res.json();
        showAlert(data.message, data.success ? 'success' : 'error');
    } catch { showAlert('Gagal menghubungi server.', 'error'); }
    finally { btn.disabled=false; btn.innerHTML='<i class="fas fa-save"></i> Simpan Ticker'; }
});

// Simpan Hero
document.getElementById('form-hero').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn-save-hero');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    const payload = {
        hero_title:    document.getElementById('hero_title').value.trim(),
        hero_subtitle: document.getElementById('hero_subtitle').value.trim(),
    };
    try {
        const res  = await fetch(API, { method:'POST', credentials:'include', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
        const data = await res.json();
        showAlert(data.message, data.success ? 'success' : 'error');
    } catch { showAlert('Gagal menghubungi server.', 'error'); }
    finally { btn.disabled=false; btn.innerHTML='<i class="fas fa-save"></i> Simpan Hero Banner'; }
});

// Simpan Sidebar Beranda
document.getElementById('form-home-sidebar').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn-save-sidebar');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    const payload = {
        about_text:      document.getElementById('about_text').value.trim(),
        contact_phone:   document.getElementById('contact_phone').value.trim(),
        contact_email:   document.getElementById('contact_email').value.trim(),
        contact_address: document.getElementById('contact_address').value.trim(),
    };
    try {
        const res  = await fetch(API, { method:'POST', credentials:'include', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
        const data = await res.json();
        showAlert(data.message, data.success ? 'success' : 'error');
    } catch { showAlert('Gagal menghubungi server.', 'error'); }
    finally { btn.disabled=false; btn.innerHTML='<i class="fas fa-save"></i> Simpan Sidebar Beranda'; }
});

// Simpan Pengumuman Beranda
document.getElementById('form-announcements').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('btn-save-announcements');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    const payload = {
        announcement_1_title: document.getElementById('announcement_1_title').value.trim(),
        announcement_1_date: document.getElementById('announcement_1_date').value.trim(),
        announcement_2_title: document.getElementById('announcement_2_title').value.trim(),
        announcement_2_date: document.getElementById('announcement_2_date').value.trim(),
        announcement_3_title: document.getElementById('announcement_3_title').value.trim(),
        announcement_3_date: document.getElementById('announcement_3_date').value.trim(),
    };
    try {
        const res  = await fetch(API, { method:'POST', credentials:'include', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
        const data = await res.json();
        showAlert(data.message, data.success ? 'success' : 'error');
    } catch { showAlert('Gagal menghubungi server.', 'error'); }
    finally { btn.disabled=false; btn.innerHTML='<i class="fas fa-save"></i> Simpan Pengumuman'; }
});

loadContents();
</script>
</body></html>
