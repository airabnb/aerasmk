<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Kelola Jurusan';
$pdo = getDB();
$jurusanList = $pdo->query("SELECT * FROM jurusan ORDER BY id ASC")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <p style="margin:0;color:var(--text-muted);">Kelola daftar kompetensi keahlian (jurusan) SMK Bangun Nusa Bangsa.</p>
    <a href="<?= e(url('admin/jurusan-form.php')) ?>" class="btn btn-primary">+ Tambah Jurusan</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Jurusan</th>
                <th>Kepala Jurusan</th>
                <th>Kuota</th>
                <th style="width:180px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($jurusanList)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--text-light);padding:32px;">Belum ada data jurusan.</td></tr>
            <?php else: ?>
                <?php foreach ($jurusanList as $j): ?>
                    <tr>
                        <td><span class="badge badge-blue"><?= e($j['kode']) ?></span></td>
                        <td>
                            <strong><?= e($j['nama']) ?></strong>
                            <div style="font-size:.82rem;color:var(--text-light);"><?= e(excerpt($j['deskripsi_singkat'], 75)) ?></div>
                        </td>
                        <td><?= e($j['kepala_jurusan'] ?: '-') ?></td>
                        <td><?= (int) $j['kuota'] ?> Siswa</td>
                        <td>
                            <div style="display:flex;gap:8px;">
                                <a href="<?= e(url('admin/jurusan-form.php?id=' . (int) $j['id'])) ?>" class="btn btn-outline btn-sm">Edit</a>
                                <form method="post" action="<?= e(url('admin/jurusan-hapus.php')) ?>" onsubmit="return confirm('Yakin ingin menghapus jurusan ini?');">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int) $j['id'] ?>">
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
