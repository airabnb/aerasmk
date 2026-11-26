<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Kompetensi Keahlian (Jurusan)';
$pdo = getDB();

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM jurusan WHERE nama LIKE :q1 OR kode LIKE :q2 OR deskripsi_singkat LIKE :q3 ORDER BY id ASC");
    $like = '%' . $q . '%';
    $stmt->execute([':q1' => $like, ':q2' => $like, ':q3' => $like]);
    $jurusanList = $stmt->fetchAll();
} else {
    $jurusanList = $pdo->query("SELECT * FROM jurusan ORDER BY id ASC")->fetchAll();
}

include __DIR__ . '/includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <span class="badge badge-amber" style="margin-bottom:12px;">Program Vokasi</span>
        <h1>Kompetensi Keahlian / Jurusan</h1>
        <p>SMK Bangun Nusa Bangsa menyelenggarakan program keahlian yang relevan dengan kebutuhan industri modern dan teknologi terkini.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <form method="get" class="filter-bar">
            <div class="search-group">
                <input type="text" name="q" class="input" placeholder="Cari jurusan (misal: PPLG, Jaringan, Desain, Akuntansi)..." value="<?= e($q) ?>">
                <button type="submit" class="btn btn-primary">Cari</button>
                <?php if ($q !== ''): ?>
                    <a href="<?= e(url('jurusan.php')) ?>" class="btn btn-outline">Reset</a>
                <?php endif; ?>
            </div>
            <div style="font-size:.9rem;color:var(--text-muted);font-weight:600;">
                Menampilkan <?= count($jurusanList) ?> kompetensi keahlian
            </div>
        </form>

        <?php if (empty($jurusanList)): ?>
            <div class="card" style="padding:40px;text-align:center;">
                <h3>Jurusan tidak ditemukan</h3>
                <p style="color:var(--text-muted);">Coba gunakan kata kunci pencarian lain.</p>
            </div>
        <?php else: ?>
            <div class="grid-2">
                <?php foreach ($jurusanList as $j): ?>
                    <article class="card">
                        <?php if (!empty($j['gambar'])): ?>
                            <div class="card-thumb" style="aspect-ratio:16/8;">
                                <img src="<?= e(url($j['gambar'])) ?>" alt="<?= e($j['nama']) ?>">
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px;">
                                <div class="jurusan-icon" style="margin-bottom:0;">
                                    <?= getJurusanIcon($j['ikon'] ?? 'cpu') ?>
                                </div>
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <span class="badge badge-blue"><?= e($j['kode']) ?></span>
                                    <span class="badge badge-slate">Kuota <?= (int) $j['kuota'] ?> Siswa</span>
                                </div>
                            </div>
                            <h2 class="card-title" style="font-size:1.3rem;"><?= e($j['nama']) ?></h2>
                            <p class="card-text"><?= e($j['deskripsi_singkat']) ?></p>

                            <?php if (!empty($j['kepala_jurusan'])): ?>
                                <p style="font-size:.86rem;color:var(--text-light);margin-bottom:16px;">
                                    Kepala Program: <strong style="color:var(--navy);"><?= e($j['kepala_jurusan']) ?></strong>
                                </p>
                            <?php endif; ?>

                            <a href="<?= e(url('jurusan-detail.php?slug=' . urlencode($j['slug']))) ?>" class="btn btn-outline btn-sm" style="align-self:flex-start;">
                                Lihat Kurikulum &amp; Prospek Karir &rarr;
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
