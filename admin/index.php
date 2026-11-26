<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Dashboard';
$pdo = getDB();

$totalArtikel   = (int) $pdo->query("SELECT COUNT(*) FROM artikel")->fetchColumn();
$totalJurusan   = (int) $pdo->query("SELECT COUNT(*) FROM jurusan")->fetchColumn();
$totalInformasi = (int) $pdo->query("SELECT COUNT(*) FROM informasi")->fetchColumn();
$totalViews     = (int) $pdo->query("SELECT COALESCE(SUM(views), 0) FROM artikel")->fetchColumn();

$latestArtikel = $pdo->query("SELECT id, judul, kategori, status, views, created_at FROM artikel ORDER BY created_at DESC LIMIT 5")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="grid-4" style="margin-bottom:28px;">
    <div class="card"><div class="card-body"><span style="color:var(--text-light);font-size:.85rem;">Total Artikel</span><strong style="font-size:1.85rem;font-family:'Plus Jakarta Sans',sans-serif;color:var(--navy);margin-top:4px;"><?= $totalArtikel ?></strong></div></div>
    <div class="card"><div class="card-body"><span style="color:var(--text-light);font-size:.85rem;">Total Jurusan</span><strong style="font-size:1.85rem;font-family:'Plus Jakarta Sans',sans-serif;color:var(--primary);margin-top:4px;"><?= $totalJurusan ?></strong></div></div>
    <div class="card"><div class="card-body"><span style="color:var(--text-light);font-size:.85rem;">Total Informasi</span><strong style="font-size:1.85rem;font-family:'Plus Jakarta Sans',sans-serif;color:var(--success);margin-top:4px;"><?= $totalInformasi ?></strong></div></div>
    <div class="card"><div class="card-body"><span style="color:var(--text-light);font-size:.85rem;">Pembaca Artikel</span><strong style="font-size:1.85rem;font-family:'Plus Jakarta Sans',sans-serif;color:#b45309;margin-top:4px;"><?= number_format($totalViews) ?></strong></div></div>
</div>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
    <h2 style="margin:0;font-size:1.25rem;">Artikel Terbaru</h2>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="<?= e(url('admin/artikel-form.php')) ?>" class="btn btn-primary btn-sm">+ Tambah Artikel</a>
        <a href="<?= e(url('admin/jurusan-form.php')) ?>" class="btn btn-outline btn-sm">+ Tambah Jurusan</a>
        <a href="<?= e(url('admin/informasi-form.php')) ?>" class="btn btn-outline btn-sm">+ Tambah Informasi</a>
    </div>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Judul Artikel</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Dibaca</th>
                <th>Tanggal</th>
                <th style="width:150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($latestArtikel)): ?>
                <tr><td colspan="6" style="text-align:center;color:var(--text-light);">Belum ada artikel.</td></tr>
            <?php else: ?>
                <?php foreach ($latestArtikel as $art): ?>
                    <tr>
                        <td><strong><?= e($art['judul']) ?></strong></td>
                        <td><span class="badge badge-blue"><?= e($art['kategori']) ?></span></td>
                        <td>
                            <span class="badge <?= $art['status'] === 'published' ? 'badge-green' : 'badge-slate' ?>">
                                <?= e($art['status'] === 'published' ? 'Terbit' : 'Draft') ?>
                            </span>
                        </td>
                        <td><?= (int) $art['views'] ?>x</td>
                        <td><?= e(formatTanggal($art['created_at'])) ?></td>
                        <td>
                            <a href="<?= e(url('admin/artikel-form.php?id=' . (int) $art['id'])) ?>" class="btn btn-outline btn-sm">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
