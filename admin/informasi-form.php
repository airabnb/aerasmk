<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getDB();
$id = (int) ($_GET['id'] ?? 0);
$isEdit = $id > 0;

$info = [
    'judul'         => '',
    'kategori'      => 'Pengumuman',
    'isi'           => '',
    'tanggal_mulai' => date('Y-m-d'),
    'lokasi'        => '',
    'is_pinned'     => 0,
];

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM informasi WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) {
        setFlash('danger', 'Informasi tidak ditemukan.');
        redirect('admin/informasi.php');
    }
    $info = $row;
}

$pageTitle = $isEdit ? 'Edit Informasi' : 'Tambah Informasi';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $errors[] = 'Token keamanan tidak valid.';
    } else {
        $judul        = trim($_POST['judul'] ?? '');
        $kategori     = trim($_POST['kategori'] ?? 'Pengumuman');
        $isi          = trim($_POST['isi'] ?? '');
        $tanggalMulai = trim($_POST['tanggal_mulai'] ?? '') ?: null;
        $lokasi       = trim($_POST['lokasi'] ?? '');
        $isPinned     = isset($_POST['is_pinned']) ? 1 : 0;

        if ($judul === '') $errors[] = 'Judul informasi wajib diisi.';
        if ($isi === '') $errors[] = 'Isi informasi wajib diisi.';

        $info = [
            'judul'         => $judul,
            'kategori'      => $kategori,
            'isi'           => $isi,
            'tanggal_mulai' => $tanggalMulai,
            'lokasi'        => $lokasi,
            'is_pinned'     => $isPinned,
        ];

        if (empty($errors)) {
            if ($isEdit) {
                $stmt = $pdo->prepare("UPDATE informasi SET judul = :judul, kategori = :kategori, isi = :isi, tanggal_mulai = :tgl, lokasi = :lokasi, is_pinned = :pinned WHERE id = :id");
                $stmt->execute([
                    ':judul'    => $judul,
                    ':kategori' => $kategori,
                    ':isi'      => $isi,
                    ':tgl'      => $tanggalMulai,
                    ':lokasi'   => $lokasi,
                    ':pinned'   => $isPinned,
                    ':id'       => $id,
                ]);
                setFlash('success', 'Informasi berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare("INSERT INTO informasi (judul, kategori, isi, tanggal_mulai, lokasi, is_pinned) VALUES (:judul, :kategori, :isi, :tgl, :lokasi, :pinned)");
                $stmt->execute([
                    ':judul'    => $judul,
                    ':kategori' => $kategori,
                    ':isi'      => $isi,
                    ':tgl'      => $tanggalMulai,
                    ':lokasi'   => $lokasi,
                    ':pinned'   => $isPinned,
                ]);
                setFlash('success', 'Informasi baru berhasil ditambahkan.');
            }

            redirect('admin/informasi.php');
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom:18px;">
    <a href="<?= e(url('admin/informasi.php')) ?>" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Informasi</a>
</div>

<div class="form-card">
    <h2 style="margin-bottom:20px;"><?= e($pageTitle) ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul style="margin:0;padding-left:18px;">
                <?php foreach ($errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <?= csrfField() ?>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="judul">Judul Informasi *</label>
                <input type="text" id="judul" name="judul" class="input" required value="<?= e($info['judul']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="kategori">Kategori</label>
                <select id="kategori" name="kategori" class="select">
                    <?php foreach (['Pengumuman', 'Agenda', 'PPDB', 'Prestasi', 'Umum'] as $kat): ?>
                        <option value="<?= e($kat) ?>" <?= $info['kategori'] === $kat ? 'selected' : '' ?>><?= e($kat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="tanggal_mulai">Tanggal</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="input" value="<?= e($info['tanggal_mulai']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="lokasi">Lokasi</label>
                <input type="text" id="lokasi" name="lokasi" class="input" placeholder="Misal: Aula Utama SMK BNB" value="<?= e($info['lokasi']) ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="isi">Isi Informasi *</label>
            <textarea id="isi" name="isi" class="textarea" rows="5" required><?= e($info['isi']) ?></textarea>
        </div>

        <div class="form-group">
            <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;font-size:.92rem;">
                <input type="checkbox" name="is_pinned" value="1" <?= !empty($info['is_pinned']) ? 'checked' : '' ?>>
                Sematkan di bagian atas (Penting)
            </label>
        </div>

        <div style="display:flex;gap:12px;">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Informasi' ?></button>
            <a href="<?= e(url('admin/informasi.php')) ?>" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
