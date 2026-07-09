<?php
/**
 * backend/contents.php  — API pengaturan konten website
 * GET  /backend/contents.php          → semua konten
 * POST /backend/contents.php          → simpan/update satu atau banyak konten
 */
declare(strict_types=1);
require_once __DIR__ . '/config.php';
header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_ORIGIN']) ? htmlspecialchars($_SERVER['HTTP_ORIGIN'], ENT_QUOTES) : '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

init_session();
$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = get_db();

    // GET — ambil semua konten (publik boleh akses untuk baca)
    if ($method === 'GET') {
        $rows = $pdo->query('SELECT content_key, content_value, description FROM contents ORDER BY id')->fetchAll();
        $data = [];
        foreach ($rows as $r) $data[$r['content_key']] = ['value' => $r['content_value'], 'description' => $r['description']];
        json_response(['success' => true, 'data' => $data]);
    }

    // POST — simpan (hanya admin/operator)
    if ($method === 'POST') {
        if (empty($_SESSION['user_id'])) json_response(['success' => false, 'message' => 'Unauthorized.'], 401);
        if (!in_array($_SESSION['role'] ?? '', ['admin', 'operator'], true))
            json_response(['success' => false, 'message' => 'Akses ditolak.'], 403);

        $body = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($body)) json_response(['success' => false, 'message' => 'Data tidak valid.'], 422);

        $stmt = $pdo->prepare(
            'INSERT INTO contents (content_key, content_value) VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE content_value = VALUES(content_value)'
        );
        foreach ($body as $key => $value) {
            if (!preg_match('/^[a-z0-9_]{1,100}$/', $key)) continue;
            $stmt->execute([':k' => $key, ':v' => (string) $value]);
        }
        json_response(['success' => true, 'message' => 'Konten berhasil disimpan.']);
    }

    json_response(['success' => false, 'message' => 'Method tidak dikenali.'], 405);
} catch (PDOException $e) {
    error_log('contents.php: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Terjadi kesalahan server.'], 500);
}
