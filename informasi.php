<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Informasi Sekolah';
$pdo = getDB();

$kat = trim($_GET['kategori'] ?? '');
$allowedKat = ['Pengumuman', 'Agenda', 'PPDB', 'Prestasi', 'Umum'];

if ($kat !== '' && in_array($kat, $allowedKat, true)) {
    $stmt = $pdo->prepare("SELECT * FROM informasi WHERE kategori = :kat ORDER BY is_pinned DESC, tanggal_mulai DESC, id DESC");
    $stmt->execute([':kat' => $kat]);
    $infoList = $stmt->fetchAll();
} else {
    $kat = '';
    $infoList = $pdo->query("SELECT * FROM informasi ORDER BY is_pinned DESC, tanggal_mulai DESC, id DESC")->fetchAll();
}

include __DIR__ . '/includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <span class="badge badge-amber" style="margin-bottom:12px;">Pusat Informasi</span>
        <h1>Informasi &amp; Profil Sekolah</h1>
        <p>Mengenal lebih dekat visi, misi, fasilitas, pengumuman resmi, serta informasi Penerimaan Peserta Didik Baru (PPDB) SMK Bangun Nusa Bangsa.</p>
    </div>
</section>

<!-- Profil, Visi & Misi -->
<section class="section">
    <div class="container">
        <div class="grid-2" style="align-items:stretch;">
            <div class="card">
                <div class="card-body">
                    <span class="section-eyebrow">Profil Singkat</span>
                    <h2 style="font-size:1.5rem;margin-bottom:12px;">SMK Bangun Nusa Bangsa</h2>
                    <p style="color:var(--text-muted);margin-bottom:14px;">
                        SMK Bangun Nusa Bangsa adalah lembaga pendidikan menengah kejuruan yang berkomitmen melahirkan generasi muda terampil, adaptif terhadap perkembangan teknologi, serta berkarakter unggul.
                    </p>
                    <p style="color:var(--text-muted);margin-bottom:0;">
                        Dengan dukungan tenaga pendidik tersertifikasi, laboratorium praktik berstandar industri, serta kemitraan aktif dengan lebih dari 45 perusahaan nasional, kami memastikan lulusan siap kerja, siap kuliah, maupun berwirausaha.
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <span class="section-eyebrow">Visi &amp; Misi</span>
                    <h2 style="font-size:1.5rem;margin-bottom:12px;">Arah &amp; Tujuan Pendidikan</h2>
                    <p style="margin-bottom:12px;"><strong>Visi:</strong> Menjadi SMK unggulan berdaya saing global yang menghasilkan lulusan profesional, inovatif, dan berakhlak mulia.</p>
                    <ul style="margin:0;padding-left:20px;color:var(--text-muted);display:grid;gap:6px;">
                        <li>Menyelenggarakan pembelajaran berbasis proyek dan standar industri (Teaching Factory).</li>
                        <li>Mengembangkan literasi digital, kewirausahaan, dan karakter Profil Pelajar Pancasila.</li>
                        <li>Memperluas jejaring kemitraan Dunia Usaha &amp; Dunia Industri (DUDI).</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Papan Pengumuman & Informasi Dinamis dari MySQL -->
<section class="section section-alt" id="ppdb">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="section-eyebrow">Papan Pengumuman</span>
                <h2>Pengumuman, Agenda &amp; PPDB</h2>
                <p>Informasi terbaru yang dikelola langsung oleh pihak sekolah.</p>
            </div>
        </div>

        <div class="filter-bar">
            <div class="pill-tabs">
                <a href="<?= e(url('informasi.php#ppdb')) ?>" class="pill-tab <?= $kat === '' ? 'active' : '' ?>">Semua</a>
                <?php foreach ($allowedKat as $item): ?>
                    <a href="<?= e(url('informasi.php?kategori=' . urlencode($item) . '#ppdb')) ?>" class="pill-tab <?= $kat === $item ? 'active' : '' ?>">
                        <?= e($item) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div style="font-size:.88rem;color:var(--text-muted);font-weight:600;">
                Total <?= count($infoList) ?> informasi
            </div>
        </div>

        <?php if (empty($infoList)): ?>
            <div class="card" style="padding:40px;text-align:center;">
                <h3>Belum ada informasi pada kategori ini</h3>
            </div>
        <?php else: ?>
            <div class="grid-2">
                <?php foreach ($infoList as $info): ?>
                    <?php
                    $ts = $info['tanggal_mulai'] ? strtotime($info['tanggal_mulai']) : strtotime($info['created_at']);
                    ?>
                    <div class="info-card">
                        <div class="info-date-box">
                            <strong><?= e(date('d', $ts)) ?></strong>
                            <span><?= e(date('M', $ts)) ?></span>
                        </div>
                        <div style="flex:1;">
                            <div class="card-meta" style="margin-bottom:8px;">
                                <span class="badge <?= $info['kategori'] === 'PPDB' ? 'badge-amber' : ($info['kategori'] === 'Prestasi' ? 'badge-green' : 'badge-blue') ?>">
                                    <?= e($info['kategori']) ?>
                                </span>
                                <?php if (!empty($info['is_pinned'])): ?>
                                    <span class="badge badge-slate">Penting</span>
                                <?php endif; ?>
                                <?php if (!empty($info['tanggal_mulai'])): ?>
                                    <span><?= e(formatTanggal($info['tanggal_mulai'])) ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="card-title" style="font-size:1.08rem;margin-bottom:8px;"><?= e($info['judul']) ?></h3>
                            <p style="margin:0 0 8px;color:var(--text-muted);font-size:.92rem;"><?= nl2br(e($info['isi'])) ?></p>
                            <?php if (!empty($info['lokasi'])): ?>
                                <div style="font-size:.84rem;color:var(--primary);font-weight:600;">
                                    Lokasi: <?= e($info['lokasi']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Fasilitas & Kontak -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="section-eyebrow">Sarana &amp; Prasarana</span>
                <h2>Fasilitas Kampus Modern</h2>
            </div>
        </div>
        <div class="grid-3">
            <div class="card"><div class="card-body"><h3 class="card-title">Lab Komputer &amp; Coding</h3><p class="card-text">Dilengkapi perangkat PC spesifikasi tinggi, koneksi internet fiber optik dedicated, dan perangkat lunak berlisensi.</p></div></div>
            <div class="card"><div class="card-body"><h3 class="card-title">Studio Kreatif &amp; Multimedia</h3><p class="card-text">Studio fotografi, ruang editing video, green screen, serta pen tablet profesional untuk jurusan DKV.</p></div></div>
            <div class="card"><div class="card-body"><h3 class="card-title">Bursa Kerja Khusus (BKK)</h3><p class="card-text">Layanan bimbingan karier, persiapan wawancara kerja, dan penyaluran magang/kerja ke puluhan mitra perusahaan.</p></div></div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
