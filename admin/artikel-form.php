<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getDB();
$id = (int) ($_GET['id'] ?? 0);
$isEdit = $id > 0;

$artikel = [
    'judul'     => '',
    'kategori'  => 'Berita',
    'ringkasan' => '',
    'konten'    => '',
    'penulis'   => $_SESSION['admin_user']['nama'] ?? 'Admin SMK BNB',
    'status'    => 'published',
    'gambar'    => null,
];

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM artikel WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) {
        setFlash('danger', 'Artikel tidak ditemukan.');
        redirect('admin/artikel.php');
    }
    $artikel = $row;
}

$pageTitle = $isEdit ? 'Edit Artikel' : 'Tambah Artikel';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $errors[] = 'Token keamanan tidak valid.';
    } else {
        $judul     = trim($_POST['judul'] ?? '');
        $kategori  = trim($_POST['kategori'] ?? 'Berita');
        $ringkasan = trim($_POST['ringkasan'] ?? '');
        $konten    = trim($_POST['konten'] ?? '');
        $penulis   = trim($_POST['penulis'] ?? 'Admin SMK BNB');
        $status    = ($_POST['status'] ?? 'published') === 'draft' ? 'draft' : 'published';

        if ($judul === '') $errors[] = 'Judul artikel wajib diisi.';
        if ($ringkasan === '') $errors[] = 'Ringkasan artikel wajib diisi.';
        if ($konten === '') $errors[] = 'Isi konten artikel wajib diisi.';

        $uploadErr = null;
        $newImage = uploadImage('gambar', 'artikel', $uploadErr);
        if ($uploadErr !== null) {
            $errors[] = $uploadErr;
        }

        $artikel = array_merge($artikel, [
            'judul'     => $judul,
            'kategori'  => $kategori,
            'ringkasan' => $ringkasan,
            'konten'    => $konten,
            'penulis'   => $penulis,
            'status'    => $status,
        ]);

        if (empty($errors)) {
            $baseSlug = slugify($judul);
            $slug = $baseSlug;
            $counter = 1;
            while (true) {
                $check = $pdo->prepare("SELECT id FROM artikel WHERE slug = :slug AND id != :id LIMIT 1");
                $check->execute([':slug' => $slug, ':id' => $id]);
                if (!$check->fetch()) {
                    break;
                }
                $slug = $baseSlug . '-' . (++$counter);
            }

            $gambarPath = $artikel['gambar'];
            if ($newImage !== null) {
                if ($isEdit && !empty($artikel['gambar'])) {
                    deleteUploadedFile($artikel['gambar']);
                }
                $gambarPath = $newImage;
            }

            if ($isEdit) {
                $stmt = $pdo->prepare("UPDATE artikel SET judul = :judul, slug = :slug, kategori = :kategori, ringkasan = :ringkasan, konten = :konten, gambar = :gambar, penulis = :penulis, status = :status WHERE id = :id");
                $stmt->execute([
                    ':judul'     => $judul,
                    ':slug'      => $slug,
                    ':kategori'  => $kategori,
                    ':ringkasan' => $ringkasan,
                    ':konten'    => $konten,
                    ':gambar'    => $gambarPath,
                    ':penulis'   => $penulis,
                    ':status'    => $status,
                    ':id'        => $id,
                ]);
                setFlash('success', 'Artikel berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare("INSERT INTO artikel (judul, slug, kategori, ringkasan, konten, gambar, penulis, status) VALUES (:judul, :slug, :kategori, :ringkasan, :konten, :gambar, :penulis, :status)");
                $stmt->execute([
                    ':judul'     => $judul,
                    ':slug'      => $slug,
                    ':kategori'  => $kategori,
                    ':ringkasan' => $ringkasan,
                    ':konten'    => $konten,
                    ':gambar'    => $gambarPath,
                    ':penulis'   => $penulis,
                    ':status'    => $status,
                ]);
                setFlash('success', 'Artikel baru berhasil ditambahkan.');
            }

            redirect('admin/artikel.php');
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom:18px;">
    <a href="<?= e(url('admin/artikel.php')) ?>" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Artikel</a>
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

    <form method="post" enctype="multipart/form-data">
        <?= csrfField() ?>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="judul">Judul Artikel *</label>
                <input type="text" id="judul" name="judul" class="input" required value="<?= e($artikel['judul']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="kategori">Kategori</label>
                <select id="kategori" name="kategori" class="select">
                    <?php foreach (['Berita', 'Prestasi', 'Kegiatan', 'Akademik', 'Pengumuman'] as $kat): ?>
                        <option value="<?= e($kat) ?>" <?= $artikel['kategori'] === $kat ? 'selected' : '' ?>><?= e($kat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label" for="penulis">Penulis</label>
                <input type="text" id="penulis" name="penulis" class="input" value="<?= e($artikel['penulis']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="status">Status Publikasi</label>
                <select id="status" name="status" class="select">
                    <option value="published" <?= $artikel['status'] === 'published' ? 'selected' : '' ?>>Terbit (Published)</option>
                    <option value="draft" <?= $artikel['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="ringkasan">Ringkasan Singkat *</label>
            <textarea id="ringkasan" name="ringkasan" class="textarea" rows="2" maxlength="300" required><?= e($artikel['ringkasan']) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="konten">Isi Artikel *</label>
            <textarea id="konten" name="konten" class="textarea" rows="10" required><?= e($artikel['konten']) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="gambar">Gambar Sampul (Opsional, maks 2MB)</label>
            <input type="file" id="gambar" name="gambar" class="input" accept="image/*" data-preview="previewImg">
            <img id="previewImg"
                 src="<?= !empty($artikel['gambar']) ? e(url($artikel['gambar'])) : '' ?>"
                 alt="Preview Gambar"
                 style="margin-top:12px;max-height:180px;border-radius:10px;<?= empty($artikel['gambar']) ? 'display:none;' : '' ?>">
        </div>

        <div style="display:flex;gap:12px;">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Simpan Perubahan' : 'Publikasikan Artikel' ?></button>
            <a href="<?= e(url('admin/artikel.php')) ?>" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
