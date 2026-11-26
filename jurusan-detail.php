<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDB();
$slug = trim($_GET['slug'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM jurusan WHERE slug = :slug LIMIT 1");
$stmt->execute([':slug' => $slug]);
$jurusan = $stmt->fetch();

if (!$jurusan) {
    http_response_code(404);
    $pageTitle = 'Jurusan Tidak Ditemukan';
    include __DIR__ . '/includes/header.php';
    echo '<div class="container section"><div class="card" style="padding:48px;text-align:center;"><h2>Jurusan Tidak Ditemukan</h2><p>Program keahlian yang Anda cari tidak tersedia.</p><a href="' . e(url('jurusan.php')) . '" class="btn btn-primary" style="margin-top:12px;">Kembali ke Daftar Jurusan</a></div></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $jurusan['nama'];
$otherStmt = $pdo->prepare("SELECT id, kode, nama, slug FROM jurusan WHERE id != :id ORDER BY id ASC");
$otherStmt->execute([':id' => $jurusan['id']]);
$otherJurusan = $otherStmt->fetchAll();

$prospekList = array_filter(array_map('trim', explode(',', $jurusan['prospek_karir'])));

include __DIR__ . '/includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <div style="display:flex;gap:10px;align-items:center;margin-bottom:12px;">
            <span class="badge badge-amber"><?= e($jurusan['kode']) ?></span>
            <span class="badge" style="background:rgba(255,255,255,.15);color:#fff;">Kuota <?= (int) $jurusan['kuota'] ?> Siswa / Tahun</span>
        </div>
        <h1><?= e($jurusan['nama']) ?></h1>
        <p><?= e($jurusan['deskripsi_singkat']) ?></p>
    </div>
</section>

<section class="section">
    <div class="container article-layout">
        <div class="article-content-card">
            <?php if (!empty($jurusan['gambar'])): ?>
                <div class="article-cover">
                    <img src="<?= e(url($jurusan['gambar'])) ?>" alt="<?= e($jurusan['nama']) ?>">
                </div>
            <?php endif; ?>

            <h2 style="margin-bottom:14px;">Tentang Kompetensi Keahlian</h2>
            <div class="article-body">
                <?= nl2br(e($jurusan['deskripsi'])) ?>
            </div>

            <hr style="border:none;border-top:1px solid var(--border);margin:28px 0;">

            <h3 style="margin-bottom:14px;">Prospek Karir Lulusan</h3>
            <div style="display:flex;flex-wrap:wrap;gap:10px;">
                <?php foreach ($prospekList as $karir): ?>
                    <span class="badge badge-blue" style="padding:8px 14px;font-size:.86rem;"><?= e($karir) ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <aside style="display:grid;gap:22px;">
            <div class="sidebar-box">
                <h3 style="font-size:1.08rem;margin-bottom:14px;">Informasi Program</h3>
                <div style="display:grid;gap:12px;font-size:.92rem;">
                    <div>
                        <span style="color:var(--text-light);display:block;">Kode Kompetensi</span>
                        <strong><?= e($jurusan['kode']) ?></strong>
                    </div>
                    <div>
                        <span style="color:var(--text-light);display:block;">Kepala Program Keahlian</span>
                        <strong><?= e($jurusan['kepala_jurusan'] ?: '-') ?></strong>
                    </div>
                    <div>
                        <span style="color:var(--text-light);display:block;">Daya Tampung</span>
                        <strong><?= (int) $jurusan['kuota'] ?> Peserta Didik</strong>
                    </div>
                    <div>
                        <span style="color:var(--text-light);display:block;">Masa Studi</span>
                        <strong>3 Tahun (Termasuk PKL Industri)</strong>
                    </div>
                </div>
                <a href="<?= e(url('informasi.php#ppdb')) ?>" class="btn btn-primary" style="width:100%;margin-top:20px;">Daftar PPDB Sekarang</a>
            </div>

            <div class="sidebar-box">
                <h3 style="font-size:1.05rem;margin-bottom:14px;">Jurusan Lainnya</h3>
                <div style="display:grid;gap:10px;">
                    <?php foreach ($otherJurusan as $other): ?>
                        <a href="<?= e(url('jurusan-detail.php?slug=' . urlencode($other['slug']))) ?>" style="padding:10px 12px;border-radius:10px;background:var(--surface-alt);display:flex;justify-content:space-between;align-items:center;font-size:.9rem;font-weight:600;">
                            <span><?= e($other['nama']) ?></span>
                            <span class="badge badge-blue"><?= e($other['kode']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
