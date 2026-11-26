<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getDB();
$id = (int) ($_GET['id'] ?? 0);
$isEdit = $id > 0;

$jurusan = [
    'kode'              => '',
    'nama'              => '',
    'deskripsi_singkat' => '',
    'deskripsi'         => '',
    'prospek_karir'     => '',
    'kepala_jurusan'    => '',
    'kuota'             => 72,
    'ikon'              => 'code',
    'gambar'            => null,
];

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM jurusan WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) {
        setFlash('danger', 'Jurusan tidak ditemukan.');
        redirect('admin/jurusan.php');
    }
    $jurusan = $row;
}

$pageTitle = $isEdit ? 'Edit Jurusan' : 'Tambah Jurusan';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $errors[] = 'Token keamanan tidak valid.';
    } else {
        $kode             = strtoupper(trim($_POST['kode'] ?? ''));
        $nama             = trim($_POST['nama'] ?? '');
        $deskripsiSingkat = trim($_POST['deskripsi_singkat'] ?? '');
        $deskripsi        = trim($_POST['deskripsi'] ?? '');
        $prospekKarir     = trim($_POST['prospek_karir'] ?? '');
        $kepalaJurusan    = trim($_POST['kepala_jurusan'] ?? '');
        $kuota            = max(1, (int) ($_POST['kuota'] ?? 72));
        $ikon             = trim($_POST['ikon'] ?? 'cpu');

        if ($kode === '') $errors[] = 'Kode jurusan wajib diisi.';
        if ($nama === '') $errors[] = 'Nama jurusan wajib diisi.';
        if ($deskripsiSingkat === '') $errors[] = 'Deskripsi singkat wajib diisi.';
        if ($deskripsi === '') $errors[] = 'Deskripsi lengkap wajib diisi.';

        $uploadErr = null;
        $newImage = uploadImage('gambar', 'jurusan', $uploadErr);
        if ($uploadErr !== null) {
            $errors[] = $uploadErr;
        }

        $jurusan = array_merge($jurusan, [
            'kode'              => $kode,
            'nama'              => $nama,
            'deskripsi_singkat' => $deskripsiSingkat,
            'deskripsi'         => $deskripsi,
            'prospek_karir'     => $prospekKarir,
            'kepala_jurusan'    => $kepalaJurusan,
            'kuota'             => $kuota,
            'ikon'              => $ikon,
        ]);

        if (empty($errors)) {
            $baseSlug = slugify($nama);
            $slug = $baseSlug;
            $counter = 1;
            while (true) {
                $check = $pdo->prepare("SELECT id FROM jurusan WHERE slug = :slug AND id != :id LIMIT 1");
                $check->execute([':slug' => $slug, ':id' => $id]);
                if (!$check->fetch()) {
                    break;
                }
                $slug = $baseSlug . '-' . (++$counter);
            }

            $gambarPath = $jurusan['gambar'];
            if ($newImage !== null) {
                if ($isEdit && !empty($jurusan['gambar'])) {
                    deleteUploadedFile($jurusan['gambar']);
                }
                $gambarPath = $newImage;
            }

            if ($isEdit) {
                $stmt = $pdo->prepare("UPDATE jurusan SET kode = :kode, nama = :nama, slug = :slug, deskripsi_singkat = :ds, deskripsi = :d, prospek_karir = :pk, kepala_jurusan = :kj, kuota = :kuota, ikon = :ikon, gambar = :gambar WHERE id = :id");
                $stmt->execute([
                    ':kode'   => $kode,
                    ':nama'   => $nama,
                    ':slug'   => $slug,
                    ':ds'     => $deskripsiSingkat,
                    ':d'      => $deskripsi,
                    ':pk'     => $prospekKarir,
                    ':kj'     => $kepalaJurusan,
                    ':kuota'  => $kuota,
                    ':ikon'   => $ikon,
                    ':gambar' => $gambarPath,
                    ':id'     => $id,
                ]);
                setFlash('success', 'Data jurusan berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare("INSERT INTO jurusan (kode, nama, slug, deskripsi_singkat, deskripsi, prospek_karir, kepala_jurusan, kuota, ikon, gambar) VALUES (:kode, :nama, :slug, :ds, :d, :pk, :kj, :kuota, :ikon, :gambar)");
                $stmt->execute([
                    ':kode'   => $kode,
                    ':nama'   => $nama,
                    ':slug'   => $slug,
                    ':ds'     => $deskripsiSingkat,
                    ':d'      => $deskripsi,
                    ':pk'     => $prospekKarir,
                    ':kj'     => $kepalaJurusan,
                    ':kuota'  => $kuota,
                    ':ikon'   => $ikon,
                    ':gambar' => $gambarPath,
                ]);
                setFlash('success', 'Jurusan baru berhasil ditambahkan.');
            }

            redirect('admin/jurusan.php');
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom:18px;">
    <a href="<?= e(url('admin/jurusan.php')) ?>" class="btn btn-outline btn-sm">&larr; Kembali ke Daftar Jurusan</a>
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
                <label class="form-label" for="kode">Kode Jurusan *</label>
                <input type="text" id="kode" name="kode" class="input" placeholder="Misal: PPLG" required value="<?= e($jurusan['kode']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="nama">Nama Kompetensi Keahlian *</label>
                <input type="text" id="nama" name="nama" class="input" required value="<?= e($jurusan['nama']) ?>">
            </div>
        </div>

        <div class="grid-3">
            <div class="form-group">
                <label class="form-label" for="kepala_jurusan">Kepala Program</label>
                <input type="text" id="kepala_jurusan" name="kepala_jurusan" class="input" value="<?= e($jurusan['kepala_jurusan']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="kuota">Kuota Siswa</label>
                <input type="number" id="kuota" name="kuota" class="input" min="1" value="<?= (int) $jurusan['kuota'] ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="ikon">Ikon Jurusan</label>
                <select id="ikon" name="ikon" class="select">
                    <?php foreach (['code' => 'Pemrograman (Code)', 'network' => 'Jaringan (Network)', 'palette' => 'Desain (Palette)', 'calculator' => 'Akuntansi (Calculator)', 'cpu' => 'Teknologi (CPU)', 'wrench' => 'Teknik (Wrench)'] as $k => $lbl): ?>
                        <option value="<?= e($k) ?>" <?= $jurusan['ikon'] === $k ? 'selected' : '' ?>><?= e($lbl) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="deskripsi_singkat">Deskripsi Singkat *</label>
            <input type="text" id="deskripsi_singkat" name="deskripsi_singkat" class="input" maxlength="255" required value="<?= e($jurusan['deskripsi_singkat']) ?>">
        </div>

        <div class="form-group">
            <label class="form-label" for="deskripsi">Deskripsi Lengkap *</label>
            <textarea id="deskripsi" name="deskripsi" class="textarea" rows="5" required><?= e($jurusan['deskripsi']) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="prospek_karir">Prospek Karir (pisahkan dengan koma)</label>
            <input type="text" id="prospek_karir" name="prospek_karir" class="input" placeholder="Web Developer, UI/UX Designer, ..." value="<?= e($jurusan['prospek_karir']) ?>">
        </div>

        <div class="form-group">
            <label class="form-label" for="gambar">Gambar Jurusan (Opsional)</label>
            <input type="file" id="gambar" name="gambar" class="input" accept="image/*" data-preview="previewJurusan">
            <img id="previewJurusan"
                 src="<?= !empty($jurusan['gambar']) ? e(url($jurusan['gambar'])) : '' ?>"
                 alt="Preview Gambar"
                 style="margin-top:12px;max-height:180px;border-radius:10px;<?= empty($jurusan['gambar']) ? 'display:none;' : '' ?>">
        </div>

        <div style="display:flex;gap:12px;">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Jurusan' ?></button>
            <a href="<?= e(url('admin/jurusan.php')) ?>" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
