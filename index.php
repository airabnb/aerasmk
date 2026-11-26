<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Beranda';
$pdo = getDB();

$jurusanList = $pdo->query("SELECT * FROM jurusan ORDER BY id ASC LIMIT 4")->fetchAll();
$infoList = $pdo->query("SELECT * FROM informasi ORDER BY is_pinned DESC, tanggal_mulai DESC, id DESC LIMIT 4")->fetchAll();
$artikelList = $pdo->query("SELECT * FROM artikel WHERE status = 'published' ORDER BY created_at DESC LIMIT 3")->fetchAll();

$totalJurusan = (int) $pdo->query("SELECT COUNT(*) FROM jurusan")->fetchColumn();
$totalArtikel = (int) $pdo->query("SELECT COUNT(*) FROM artikel WHERE status = 'published'")->fetchColumn();

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container hero-grid">
        <div>
            <div class="hero-badge">
                <span>&#9733;</span> Akreditasi A &bull; SMK Pusat Keunggulan
            </div>
            <h1>Wujudkan Masa Depan Cerah Bersama <span>SMK Bangun Nusa Bangsa</span></h1>
            <p>
                Pendidikan vokasi modern berbasis industri dengan kurikulum link-and-match, fasilitas laboratorium berstandar nasional, dan penyaluran kerja BKK ke perusahaan mitra terkemuka.
            </p>
            <div class="hero-cta">
                <a href="<?= e(url('jurusan.php')) ?>" class="btn btn-accent">Jelajahi Jurusan &rarr;</a>
                <a href="<?= e(url('informasi.php')) ?>" class="btn btn-white-outline">Informasi Sekolah</a>
            </div>
        </div>

        <div class="hero-panel">
            <div class="hero-panel-title">Sekilas SMK Bangun Nusa Bangsa</div>
            <div class="stat-grid">
                <div class="stat-box">
                    <strong><?= $totalJurusan ?></strong>
                    <span>Kompetensi Keahlian</span>
                </div>
                <div class="stat-box">
                    <strong>94%</strong>
                    <span>Serapan Lulusan Industri</span>
                </div>
                <div class="stat-box">
                    <strong>45+</strong>
                    <span>Mitra Dunia Usaha (DUDI)</span>
                </div>
                <div class="stat-box">
                    <strong><?= $totalArtikel ?>+</strong>
                    <span>Berita &amp; Prestasi</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Program Keahlian / Jurusan -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="section-eyebrow">Kompetensi Keahlian</span>
                <h2>Program Jurusan Unggulan</h2>
                <p>Pilih bidang keahlian sesuai minat dan bakatmu untuk siap bersaing di era industri digital dan global.</p>
            </div>
            <a href="<?= e(url('jurusan.php')) ?>" class="btn btn-outline">Lihat Semua Jurusan &rarr;</a>
        </div>

        <div class="grid-4">
            <?php foreach ($jurusanList as $j): ?>
                <article class="card">
                    <div class="card-body">
                        <div class="jurusan-icon">
                            <?= getJurusanIcon($j['ikon'] ?? 'cpu') ?>
                        </div>
                        <div style="margin-bottom:10px;">
                            <span class="badge badge-blue"><?= e($j['kode']) ?></span>
                        </div>
                        <h3 class="card-title"><?= e($j['nama']) ?></h3>
                        <p class="card-text"><?= e($j['deskripsi_singkat']) ?></p>
                        <a href="<?= e(url('jurusan-detail.php?slug=' . urlencode($j['slug']))) ?>" class="card-footer-link">
                            Detail Jurusan <span>&rarr;</span>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Informasi & Pengumuman Sekolah -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="section-eyebrow">Papan Informasi</span>
                <h2>Informasi &amp; Agenda Terkini</h2>
                <p>Pengumuman resmi, jadwal akademik, program beasiswa, dan informasi PPDB SMK Bangun Nusa Bangsa.</p>
            </div>
            <a href="<?= e(url('informasi.php')) ?>" class="btn btn-outline">Semua Informasi &rarr;</a>
        </div>

        <div class="grid-2">
            <?php foreach ($infoList as $info): ?>
                <?php
                $ts = $info['tanggal_mulai'] ? strtotime($info['tanggal_mulai']) : strtotime($info['created_at']);
                $tgl = date('d', $ts);
                $bln = date('M', $ts);
                ?>
                <div class="info-card">
                    <div class="info-date-box">
                        <strong><?= e($tgl) ?></strong>
                        <span><?= e($bln) ?></span>
                    </div>
                    <div style="flex:1;">
                        <div class="card-meta" style="margin-bottom:6px;">
                            <span class="badge <?= $info['kategori'] === 'PPDB' ? 'badge-amber' : ($info['kategori'] === 'Prestasi' ? 'badge-green' : 'badge-blue') ?>">
                                <?= e($info['kategori']) ?>
                            </span>
                            <?php if (!empty($info['is_pinned'])): ?>
                                <span class="badge badge-slate">Disematkan</span>
                            <?php endif; ?>
                            <?php if (!empty($info['lokasi'])): ?>
                                <span>&bull; <?= e($info['lokasi']) ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="card-title" style="font-size:1.05rem;margin-bottom:6px;"><?= e($info['judul']) ?></h3>
                        <p style="margin:0;color:var(--text-muted);font-size:.91rem;"><?= e(excerpt($info['isi'], 145)) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Artikel & Berita Terbaru -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="section-eyebrow">Warta Sekolah</span>
                <h2>Artikel &amp; Berita Terbaru</h2>
                <p>Liputan kegiatan siswa, prestasi kejuaraan, kunjungan industri, dan artikel edukasi dari SMK Bangun Nusa Bangsa.</p>
            </div>
            <a href="<?= e(url('artikel.php')) ?>" class="btn btn-outline">Semua Artikel &rarr;</a>
        </div>

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
                        <h3 class="card-title">
                            <a href="<?= e(url('artikel-detail.php?slug=' . urlencode($art['slug']))) ?>"><?= e($art['judul']) ?></a>
                        </h3>
                        <p class="card-text"><?= e($art['ringkasan']) ?></p>
                        <a href="<?= e(url('artikel-detail.php?slug=' . urlencode($art['slug']))) ?>" class="card-footer-link">
                            Baca Selengkapnya <span>&rarr;</span>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Banner -->
<section class="section" style="padding-top:16px;">
    <div class="container">
        <div class="cta-banner">
            <div>
                <h2>Siap Bergabung Bersama SMK Bangun Nusa Bangsa?</h2>
                <p>Penerimaan Peserta Didik Baru (PPDB) Tahun Ajaran 2026/2027 telah dibuka. Dapatkan informasi jalur prestasi dan beasiswa sekarang.</p>
            </div>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                <a href="<?= e(url('informasi.php#ppdb')) ?>" class="btn btn-accent">Lihat Info PPDB</a>
                <a href="<?= e(url('jurusan.php')) ?>" class="btn btn-white-outline">Pilih Jurusan</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
