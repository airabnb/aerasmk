<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDB();
$slug = trim($_GET['slug'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM artikel WHERE slug = :slug AND status = 'published' LIMIT 1");
$stmt->execute([':slug' => $slug]);
$artikel = $stmt->fetch();

if (!$artikel) {
    http_response_code(404);
    $pageTitle = 'Artikel Tidak Ditemukan';
    include __DIR__ . '/includes/header.php';
    echo '<div class="container section"><div class="card" style="padding:48px;text-align:center;"><h2>Artikel Tidak Ditemukan</h2><p>Artikel yang Anda cari tidak tersedia atau telah dihapus.</p><a href="' . e(url('artikel.php')) . '" class="btn btn-primary" style="margin-top:12px;">Kembali ke Daftar Artikel</a></div></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

// Tambah view count
$pdo->prepare("UPDATE artikel SET views = views + 1 WHERE id = :id")->execute([':id' => $artikel['id']]);

$relatedStmt = $pdo->prepare("SELECT id, judul, slug, kategori, created_at FROM artikel WHERE status = 'published' AND id != :id ORDER BY created_at DESC LIMIT 4");
$relatedStmt->execute([':id' => $artikel['id']]);
$relatedList = $relatedStmt->fetchAll();

$pageTitle = $artikel['judul'];
include __DIR__ . '/includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <div style="display:flex;gap:10px;align-items:center;margin-bottom:12px;flex-wrap:wrap;">
            <span class="badge badge-amber"><?= e($artikel['kategori']) ?></span>
            <span style="font-size:.88rem;color:#cbd5e1;"><?= e(formatTanggal($artikel['created_at'])) ?></span>
            <span style="font-size:.88rem;color:#cbd5e1;">&bull; Ditulis oleh <?= e($artikel['penulis']) ?></span>
            <span style="font-size:.88rem;color:#cbd5e1;">&bull; <?= (int) $artikel['views'] + 1 ?> kali dibaca</span>
        </div>
        <h1 style="max-width:860px;"><?= e($artikel['judul']) ?></h1>
    </div>
</section>

<section class="section">
    <div class="container article-layout">
        <article class="article-content-card">
            <?php if (!empty($artikel['gambar'])): ?>
                <div class="article-cover">
                    <img src="<?= e(url($artikel['gambar'])) ?>" alt="<?= e($artikel['judul']) ?>">
                </div>
            <?php endif; ?>

            <p style="font-size:1.1rem;font-weight:600;color:var(--navy);margin-bottom:20px;">
                <?= e($artikel['ringkasan']) ?>
            </p>

            <div class="article-body">
                <?php
                $paragraphs = preg_split('/\R{2,}/', trim($artikel['konten']));
                foreach ($paragraphs as $p) {
                    echo '<p>' . nl2br(e(trim($p))) . '</p>';
                }
                ?>
            </div>

            <hr style="border:none;border-top:1px solid var(--border);margin:28px 0;">
            <a href="<?= e(url('artikel.php')) ?>" class="btn btn-outline btn-sm">&larr; Kembali ke Semua Artikel</a>
        </article>

        <aside style="display:grid;gap:22px;">
            <div class="sidebar-box">
                <h3 style="font-size:1.05rem;margin-bottom:14px;">Artikel Terbaru Lainnya</h3>
                <div style="display:grid;gap:14px;">
                    <?php foreach ($relatedList as $rel): ?>
                        <a href="<?= e(url('artikel-detail.php?slug=' . urlencode($rel['slug']))) ?>" style="display:block;padding-bottom:12px;border-bottom:1px solid var(--border);">
                            <span class="badge badge-blue" style="margin-bottom:6px;"><?= e($rel['kategori']) ?></span>
                            <div style="font-weight:700;font-size:.93rem;color:var(--navy);margin-bottom:4px;"><?= e($rel['judul']) ?></div>
                            <div style="font-size:.8rem;color:var(--text-light);"><?= e(formatTanggal($rel['created_at'])) ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
