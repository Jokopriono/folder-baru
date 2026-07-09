<?php
/**
 * backend/news.php  — API CRUD berita
 * GET    /backend/news.php            → daftar berita
 * GET    /backend/news.php?id=N       → detail berita
 * POST   /backend/news.php            → tambah berita
 * PUT    /backend/news.php?id=N       → update berita
 * DELETE /backend/news.php?id=N       → hapus berita
 */
declare(strict_types=1);
require_once __DIR__ . '/config.php';
header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_ORIGIN']) ? htmlspecialchars($_SERVER['HTTP_ORIGIN'], ENT_QUOTES) : '*'));
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

init_session();
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

function makeSlug(string $text): string {
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return substr(trim($text, '-'), 0, 200) . '-' . time();
}

try {
    $pdo = get_db();

    // GET — publik boleh baca
    if ($method === 'GET') {
        if ($id > 0) {
            $stmt = $pdo->prepare('SELECT * FROM news WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            if (!$row) json_response(['success' => false, 'message' => 'Berita tidak ditemukan.'], 404);
            json_response(['success' => true, 'data' => $row]);
        }

        $limit  = min((int)($_GET['limit'] ?? 10), 50);
        $offset = max((int)($_GET['offset'] ?? 0), 0);
        $onlyPublished = empty($_SESSION['user_id']);

        $sql    = 'SELECT id, title, slug, summary, image_url, is_published, published_at FROM news';
        $params = [];
        if ($onlyPublished) { $sql .= ' WHERE is_published = 1'; }
        $sql .= ' ORDER BY published_at DESC LIMIT :limit OFFSET :offset';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $countSql = 'SELECT COUNT(*) FROM news' . ($onlyPublished ? ' WHERE is_published = 1' : '');
        $total    = (int)$pdo->query($countSql)->fetchColumn();

        json_response(['success' => true, 'data' => $stmt->fetchAll(), 'total' => $total]);
    }

    // Mutasi — hanya admin/operator
    if (empty($_SESSION['user_id'])) json_response(['success' => false, 'message' => 'Unauthorized.'], 401);
    if (!in_array($_SESSION['role'] ?? '', ['admin', 'operator'], true))
        json_response(['success' => false, 'message' => 'Akses ditolak.'], 403);

    $body = json_decode((string)file_get_contents('php://input'), true) ?? [];

    if ($method === 'POST') {
        $title        = trim((string)($body['title']        ?? ''));
        $summary      = trim((string)($body['summary']      ?? ''));
        $body_content = trim((string)($body['body']         ?? ''));
        $image_url    = trim((string)($body['image_url']    ?? ''));
        $is_published = isset($body['is_published']) ? (int)(bool)$body['is_published'] : 1;

        if (!$title || !$summary || !$body_content)
            json_response(['success' => false, 'message' => 'Judul, ringkasan, dan isi berita wajib diisi.'], 422);

        $slug = makeSlug($title);
        $stmt = $pdo->prepare(
            'INSERT INTO news (title, slug, summary, body, image_url, author_id, is_published)
             VALUES (:title, :slug, :summary, :body, :image_url, :author_id, :is_published)'
        );
        $stmt->execute([
            ':title'        => $title,
            ':slug'         => $slug,
            ':summary'      => $summary,
            ':body'         => $body_content,
            ':image_url'    => $image_url ?: null,
            ':author_id'    => $_SESSION['user_id'],
            ':is_published' => $is_published,
        ]);
        json_response(['success' => true, 'message' => 'Berita berhasil ditambahkan.', 'id' => (int)$pdo->lastInsertId()]);
    }

    if ($method === 'PUT') {
        if ($id <= 0) json_response(['success' => false, 'message' => 'ID tidak valid.'], 422);

        $title        = trim((string)($body['title']     ?? ''));
        $summary      = trim((string)($body['summary']   ?? ''));
        $body_content = trim((string)($body['body']      ?? ''));
        $image_url    = trim((string)($body['image_url'] ?? ''));
        $is_published = isset($body['is_published']) ? (int)(bool)$body['is_published'] : 1;

        if (!$title || !$summary || !$body_content)
            json_response(['success' => false, 'message' => 'Judul, ringkasan, dan isi wajib diisi.'], 422);

        $pdo->prepare(
            'UPDATE news SET title=:title, summary=:summary, body=:body,
             image_url=:image_url, is_published=:is_published WHERE id=:id'
        )->execute([
            ':title'        => $title,
            ':summary'      => $summary,
            ':body'         => $body_content,
            ':image_url'    => $image_url ?: null,
            ':is_published' => $is_published,
            ':id'           => $id,
        ]);
        json_response(['success' => true, 'message' => 'Berita berhasil diperbarui.']);
    }

    if ($method === 'DELETE') {
        if ($id <= 0) json_response(['success' => false, 'message' => 'ID tidak valid.'], 422);
        $pdo->prepare('DELETE FROM news WHERE id = :id')->execute([':id' => $id]);
        json_response(['success' => true, 'message' => 'Berita berhasil dihapus.']);
    }

    json_response(['success' => false, 'message' => 'Method tidak dikenali.'], 405);
} catch (PDOException $e) {
    error_log('news.php: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Terjadi kesalahan server.'], 500);
}
