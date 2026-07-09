<?php
declare(strict_types=1);

$page_title  = 'Ganti Password';
$active_menu = 'change-password';

require_once __DIR__ . '/_guard.php';
require_once __DIR__ . '/_layout.php';
?>

<!-- MAIN CONTENT -->
<main class="main-wrap">
    <div class="page-header">
        <div class="breadcrumb"><a href="dashboard.php">Dashboard</a> / Ganti Password</div>
        <h2><i class="fas fa-key" style="color:var(--secondary)"></i> Ganti Password</h2>
        <p>Perbarui password akun Anda. Gunakan password yang kuat dan unik.</p>
    </div>

    <div style="max-width:480px">
        <div class="card">
            <div class="card-title"><i class="fas fa-user-shield"></i> Keamanan Akun</div>

            <div id="cp-alert" style="display:none"></div>

            <form id="cp-form" novalidate autocomplete="off">
                <div class="form-group">
                    <label for="current-pw">Password Saat Ini <span style="color:red">*</span></label>
                    <div style="position:relative">
                        <input type="password" id="current-pw" placeholder="Masukkan password saat ini" required autocomplete="current-password">
                        <button type="button" class="toggle-eye" data-target="current-pw" title="Tampilkan/sembunyikan" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:.95rem"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="new-pw">Password Baru <span style="color:red">*</span></label>
                    <div style="position:relative">
                        <input type="password" id="new-pw" placeholder="Min. 8 karakter" required autocomplete="new-password">
                        <button type="button" class="toggle-eye" data-target="new-pw" title="Tampilkan/sembunyikan" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:.95rem"><i class="fas fa-eye"></i></button>
                    </div>
                    <!-- Strength bar -->
                    <div id="strength-bar" style="display:none;margin-top:8px">
                        <div style="height:5px;border-radius:4px;background:var(--border)">
                            <div id="strength-fill" style="height:100%;border-radius:4px;width:0;transition:width .3s,background .3s"></div>
                        </div>
                        <span id="strength-label" style="font-size:.78rem;color:var(--text-muted)"></span>
                    </div>
                    <div class="form-hint">Minimal 8 karakter. Kombinasikan huruf, angka, dan simbol.</div>
                </div>
                <div class="form-group">
                    <label for="confirm-pw">Konfirmasi Password Baru <span style="color:red">*</span></label>
                    <div style="position:relative">
                        <input type="password" id="confirm-pw" placeholder="Ulangi password baru" required autocomplete="new-password">
                        <button type="button" class="toggle-eye" data-target="confirm-pw" title="Tampilkan/sembunyikan" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:.95rem"><i class="fas fa-eye"></i></button>
                    </div>
                    <div class="form-hint" id="match-hint" style="display:none"></div>
                </div>

                <button type="submit" class="btn btn-primary" id="btn-change" style="width:100%;justify-content:center;margin-top:4px">
                    <i class="fas fa-save"></i> Perbarui Password
                </button>
            </form>
        </div>

        <!-- Tips keamanan -->
        <div class="card">
            <div class="card-title"><i class="fas fa-shield-alt"></i> Tips Keamanan</div>
            <ul style="padding-left:20px;font-size:.875rem;color:var(--text-muted);line-height:1.9">
                <li>Gunakan minimal 8 karakter</li>
                <li>Kombinasikan huruf besar, huruf kecil, angka, dan simbol</li>
                <li>Jangan gunakan nama, tanggal lahir, atau kata yang mudah ditebak</li>
                <li>Jangan bagikan password kepada siapapun</li>
                <li>Ganti password secara berkala</li>
            </ul>
        </div>
    </div>
</main>

<script>
// ── Toggle show/hide password ─────────────────────────────
document.querySelectorAll('.toggle-eye').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        btn.querySelector('i').className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
    });
});

// ── Password strength ─────────────────────────────────────
document.getElementById('new-pw').addEventListener('input', function () {
    const val = this.value;
    const bar  = document.getElementById('strength-bar');
    const fill = document.getElementById('strength-fill');
    const lbl  = document.getElementById('strength-label');

    if (!val) { bar.style.display = 'none'; return; }
    bar.style.display = 'block';

    let score = 0;
    if (val.length >= 8)  score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { label: 'Sangat Lemah', color: '#e74c3c', pct: '15%' },
        { label: 'Lemah',        color: '#e67e22', pct: '30%' },
        { label: 'Sedang',       color: '#f39c12', pct: '55%' },
        { label: 'Kuat',         color: '#27ae60', pct: '80%' },
        { label: 'Sangat Kuat',  color: '#1a5a3d', pct: '100%' },
    ];
    const lvl = levels[Math.min(score, 4)];
    fill.style.width      = lvl.pct;
    fill.style.background = lvl.color;
    lbl.textContent       = lvl.label;
    lbl.style.color       = lvl.color;
});

// ── Confirm match hint ────────────────────────────────────
document.getElementById('confirm-pw').addEventListener('input', function () {
    const hint = document.getElementById('match-hint');
    const newPw = document.getElementById('new-pw').value;
    hint.style.display = 'block';
    if (this.value === newPw) {
        hint.textContent = '✔ Password cocok.';
        hint.style.color = 'var(--success)';
    } else {
        hint.textContent = '✘ Password tidak cocok.';
        hint.style.color = 'var(--danger)';
    }
});

// ── Form submit ───────────────────────────────────────────
document.getElementById('cp-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const alert = document.getElementById('cp-alert');
    alert.style.display = 'none';

    const current = document.getElementById('current-pw').value;
    const newPw   = document.getElementById('new-pw').value;
    const confirm = document.getElementById('confirm-pw').value;

    if (newPw !== confirm) {
        alert.innerHTML = '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i>Konfirmasi password tidak cocok.</div>';
        alert.style.display = 'block';
        return;
    }

    const btn = document.getElementById('btn-change');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

    try {
        const res  = await fetch('../backend/change_password.php', {
            method:      'POST',
            credentials: 'include',
            headers:     { 'Content-Type': 'application/json' },
            body:        JSON.stringify({ current_password: current, new_password: newPw, confirm_password: confirm }),
        });
        const data = await res.json();

        const type = data.success ? 'success' : 'error';
        const icon = data.success ? 'check-circle' : 'exclamation-circle';
        alert.innerHTML = `<div class="alert alert-${type}"><i class="fas fa-${icon}"></i>${data.message}</div>`;
        alert.style.display = 'block';

        if (data.success) {
            document.getElementById('cp-form').reset();
            document.getElementById('strength-bar').style.display = 'none';
            document.getElementById('match-hint').style.display   = 'none';
        }
    } catch {
        alert.innerHTML = '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i>Gagal menghubungi server.</div>';
        alert.style.display = 'block';
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Perbarui Password';
    }
});
</script>

</body>
</html>
