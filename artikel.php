<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Artikel & Berita';
$pdo = getDB();

$q = trim($_GET['q'] ?? '');
$kat = trim($_GET['kategori'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 6;
$offset = ($page - 1) * $perPage;

$where = ["status = 'published'"];
$params = [];

if ($q !== '') {
    $where[] = "(judul LIKE :q1 OR ringkasan LIKE :q2 OR konten LIKE :q3)";
    $like = '%' . $q . '%';
    $params[':q1'] = $like;
    $params[':q2'] = $like;
    $params[':q3'] = $like;
}

if ($kat !== '') {
    $where[] = "kategori = :kat";
    $params[':kat'] = $kat;
}

$whereSql = implode(' AND ', $where);

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM artikel WHERE $whereSql");
$countStmt->execute($params);
$totalRows = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalRows / $perPage));

$sql = "SELECT * FROM artikel WHERE $whereSql ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$artikelList = $stmt->fetchAll();

$kategoriList = $pdo->query("SELECT DISTINCT kategori FROM artikel WHERE status = 'published' ORDER BY kategori ASC")->fetchAll(PDO::FETCH_COLUMN);

include __DIR__ . '/includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <span class="badge badge-amber" style="margin-bottom:12px;">Warta &amp; Edukasi</span>
        <h1>Artikel &amp; Berita Sekolah</h1>
        <p>Kumpulan berita terbaru, liputan prestasi, kegiatan siswa, dan artikel inspiratif SMK Bangun Nusa Bangsa.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <form method="get" class="filter-bar">
            <div class="pill-tabs">
                <a href="<?= e(url('artikel.php')) ?>" class="pill-tab <?= $kat === '' ? 'active' : '' ?>">Semua</a>
                <?php foreach ($kategoriList as $item): ?>
                    <a href="<?= e(url('artikel.php?kategori=' . urlencode($item) . ($q !== '' ? '&q=' . urlencode($q) : ''))) ?>" class="pill-tab <?= $kat === $item ? 'active' : '' ?>">
                        <?= e($item) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="search-group" style="max-width:340px;">
                <?php if ($kat !== ''): ?>
                    <input type="hidden" name="kategori" value="<?= e($kat) ?>">
                <?php endif; ?>
                <input type="text" name="q" class="input" placeholder="Cari artikel..." value="<?= e($q) ?>">
                <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            </div>
        </form>

        <?php if (empty($artikelList)): ?>
            <div class="card" style="padding:48px;text-align:center;">
                <h3>Belum ada artikel ditemukan</h3>
                <p style="color:var(--text-muted);">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
                <a href="<?= e(url('artikel.php')) ?>" class="btn btn-outline" style="margin-top:10px;">Reset Filter</a>
            </div>
        <?php else: ?>
            <div class="grid-3">
                <?php foreach ($artikelList as $art): ?>
                    <article class="card">
                        <a href="<?= e(url('artikel-detail.php?slug=' . urlencode($art['slug']))) ?>" class="card-thumb">
                            <?php if (!empty($art['gambar'])): ?>
                                <img src="<?= e(url($art['gambar'])) ?>" alt="<?= e($art['judul']) ?>">
                            <?php else: ?>
                                <div class="card-thumb-placeholder"><?= e($art['kategori']) ?> &bull; SMK BNB</div>
                            <?php endif; ?>
                        </a>
                        <div class="card-body">
                            <div class="card-meta">
                                <span class="badge badge-blue"><?= e($art['kategori']) ?></span>
                                <span><?= e(formatTanggal($art['created_at'])) ?></span>
                            </div>
                            <h2 class="card-title">
                                <a href="<?= e(url('artikel-detail.php?slug=' . urlencode($art['slug']))) ?>"><?= e($art['judul']) ?></a>
                            </h2>
                            <p class="card-text"><?= e($art['ringkasan']) ?></p>
                            <a href="<?= e(url('artikel-detail.php?slug=' . urlencode($art['slug']))) ?>" class="card-footer-link">
                                Baca Selengkapnya &rarr;
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div style="display:flex;justify-content:center;gap:8px;margin-top:36px;flex-wrap:wrap;">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php
                        $link = url('artikel.php?page=' . $i . ($kat !== '' ? '&kategori=' . urlencode($kat) : '') . ($q !== '' ? '&q=' . urlencode($q) : ''));
                        ?>
                        <a href="<?= e($link) ?>" class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-outline' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
