<?php
/**
 * Script Setup Otomatis Database SMK Bangun Nusa Bangsa
 * Jalankan sekali melalui browser: http://localhost/aerasmk/setup.php
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$status = [];
$hasError = false;

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;charset=utf8mb4', DB_HOST, DB_PORT),
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $sqlFile = __DIR__ . '/database.sql';
    if (!is_file($sqlFile)) {
        throw new RuntimeException('File database.sql tidak ditemukan.');
    }

    $sql = file_get_contents($sqlFile);
    // Pisahkan statement SQL secara aman
    $statements = array_filter(array_map('trim', preg_split('/;\s*[\r\n]+/', $sql)));
    foreach ($statements as $stmt) {
        if ($stmt !== '' && strpos($stmt, '--') !== 0) {
            $pdo->exec($stmt);
        }
    }

    $status[] = 'Database <strong>' . htmlspecialchars(DB_NAME) . '</strong> dan seluruh tabel berhasil dibuat.';
    $status[] = 'Data awal (Admin, 4 Jurusan, 4 Artikel, 4 Informasi) berhasil dimasukkan.';
} catch (Throwable $e) {
    $hasError = true;
    $status[] = 'Gagal melakukan setup: ' . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database - SMK Bangun Nusa Bangsa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; color: #0f172a; display: grid; place-items: center; min-height: 100vh; margin: 0; padding: 20px; }
        .card { background: #fff; max-width: 560px; width: 100%; border-radius: 16px; padding: 32px; box-shadow: 0 20px 40px -15px rgba(15, 23, 42, .12); border: 1px solid #e2e8f0; }
        h1 { margin: 0 0 8px; font-size: 1.5rem; }
        .sub { color: #64748b; margin: 0 0 24px; font-size: .95rem; }
        .alert { padding: 14px 16px; border-radius: 10px; margin-bottom: 12px; font-size: .92rem; }
        .ok { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .err { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .cred { background: #eff6ff; border: 1px solid #bfdbfe; padding: 14px 16px; border-radius: 10px; margin: 18px 0; font-size: .9rem; color: #1e3a8a; }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 24px; }
        .btn { text-decoration: none; padding: 11px 18px; border-radius: 10px; font-weight: 600; font-size: .92rem; display: inline-block; }
        .btn-primary { background: #1d4ed8; color: #fff; }
        .btn-outline { border: 1px solid #cbd5e1; color: #334155; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Setup Website SMK Bangun Nusa Bangsa</h1>
        <p class="sub">Inisialisasi database MySQL dan data awal website.</p>
        <?php foreach ($status as $msg): ?>
            <div class="alert <?= $hasError ? 'err' : 'ok' ?>"><?= $msg ?></div>
        <?php endforeach; ?>
        <?php if (!$hasError): ?>
            <div class="cred">
                <strong>Akun Admin Default:</strong><br>
                Username: <code>admin</code><br>
                Password: <code>admin123</code>
            </div>
            <div class="actions">
                <a class="btn btn-primary" href="<?= htmlspecialchars(url('index.php')) ?>">Buka Website Utama</a>
                <a class="btn btn-outline" href="<?= htmlspecialchars(url('admin/login.php')) ?>">Masuk Panel Admin</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
