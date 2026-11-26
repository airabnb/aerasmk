<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Kelola Informasi';
$pdo = getDB();
$infoList = $pdo->query("SELECT * FROM informasi ORDER BY is_pinned DESC, tanggal_mulai DESC, id DESC")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <p style="margin:0;color:var(--text-muted);">Kelola pengumuman, agenda, informasi PPDB, dan prestasi sekolah.</p>
    <a href="<?= e(url('admin/informasi-form.php')) ?>" class="btn btn-primary">+ Tambah Informasi</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Judul Informasi</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th>Lokasi</th>
                <th style="width:180px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($infoList)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--text-light);padding:32px;">Belum ada data informasi.</td></tr>
            <?php else: ?>
                <?php foreach ($infoList as $info): ?>
                    <tr>
                        <td>
                            <strong><?= e($info['judul']) ?></strong>
                            <?php if (!empty($info['is_pinned'])): ?>
                                <span class="badge badge-amber" style="margin-left:6px;">Disematkan</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge badge-blue"><?= e($info['kategori']) ?></span></td>
                        <td><?= e(formatTanggal($info['tanggal_mulai'])) ?></td>
                        <td><?= e($info['lokasi'] ?: '-') ?></td>
                        <td>
                            <div style="display:flex;gap:8px;">
                                <a href="<?= e(url('admin/informasi-form.php?id=' . (int) $info['id'])) ?>" class="btn btn-outline btn-sm">Edit</a>
                                <form method="post" action="<?= e(url('admin/informasi-hapus.php')) ?>" onsubmit="return confirm('Yakin ingin menghapus informasi ini?');">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int) $info['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
