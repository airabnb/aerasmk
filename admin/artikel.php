<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Kelola Artikel';
$pdo = getDB();

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM artikel WHERE judul LIKE :q1 OR kategori LIKE :q2 ORDER BY created_at DESC");
    $like = '%' . $q . '%';
    $stmt->execute([':q1' => $like, ':q2' => $like]);
    $artikelList = $stmt->fetchAll();
} else {
    $artikelList = $pdo->query("SELECT * FROM artikel ORDER BY created_at DESC")->fetchAll();
}

include __DIR__ . '/includes/header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:20px;">
    <form method="get" style="display:flex;gap:8px;flex:1;max-width:400px;">
        <input type="text" name="q" class="input" placeholder="Cari judul atau kategori artikel..." value="<?= e($q) ?>">
        <button type="submit" class="btn btn-outline btn-sm">Cari</button>
        <?php if ($q !== ''): ?>
            <a href="<?= e(url('admin/artikel.php')) ?>" class="btn btn-outline btn-sm">Reset</a>
        <?php endif; ?>
    </form>
    <a href="<?= e(url('admin/artikel-form.php')) ?>" class="btn btn-primary">+ Tambah Artikel</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Artikel</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Views</th>
                <th>Tanggal</th>
                <th style="width:180px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($artikelList)): ?>
                <tr><td colspan="6" style="text-align:center;color:var(--text-light);padding:32px;">Belum ada data artikel.</td></tr>
            <?php else: ?>
                <?php foreach ($artikelList as $art): ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <?php if (!empty($art['gambar'])): ?>
                                    <img src="<?= e(url($art['gambar'])) ?>" alt="" style="width:52px;height:38px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                                <?php endif; ?>
                                <div>
                                    <strong><?= e($art['judul']) ?></strong>
                                    <div style="font-size:.8rem;color:var(--text-light);"><?= e($art['penulis']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-blue"><?= e($art['kategori']) ?></span></td>
                        <td>
                            <span class="badge <?= $art['status'] === 'published' ? 'badge-green' : 'badge-slate' ?>">
                                <?= e($art['status'] === 'published' ? 'Terbit' : 'Draft') ?>
                            </span>
                        </td>
                        <td><?= (int) $art['views'] ?></td>
                        <td><?= e(formatTanggal($art['created_at'])) ?></td>
                        <td>
                            <div style="display:flex;gap:8px;">
                                <a href="<?= e(url('admin/artikel-form.php?id=' . (int) $art['id'])) ?>" class="btn btn-outline btn-sm">Edit</a>
                                <form method="post" action="<?= e(url('admin/artikel-hapus.php')) ?>" onsubmit="return confirm('Yakin ingin menghapus artikel ini?');">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int) $art['id'] ?>">
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
