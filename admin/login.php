<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect('admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $error = 'Sesi formulir tidak valid. Silakan muat ulang halaman.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Username dan password wajib diisi.';
        } else {
            $pdo = getDB();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
            $stmt->execute([':u' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_user'] = [
                    'id'       => $user['id'],
                    'nama'     => $user['nama'],
                    'username' => $user['username'],
                    'role'     => $user['role'],
                ];
                setFlash('success', 'Selamat datang kembali, ' . $user['nama'] . '!');
                redirect('admin/index.php');
            } else {
                $error = 'Username atau password salah.';
            }
        }
    }
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
    <style>
        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            background: radial-gradient(circle at 80% 15%, rgba(37, 99, 235, 0.28), transparent 45%),
                        linear-gradient(135deg, #0b1120 0%, #0f172a 60%, #172554 100%);
        }
        .login-card {
            width: 100%;
            max-width: 430px;
            background: #fff;
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.45);
        }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: .88rem; font-weight: 600; color: var(--navy); margin-bottom: 6px; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand" style="margin-bottom:24px;">
            <div class="brand-logo" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
            </div>
            <div class="brand-text">
                <strong>Panel Admin Sekolah</strong>
                <span>SMK Bangun Nusa Bangsa</span>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <?= csrfField() ?>
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" id="username" name="username" class="input" placeholder="Masukkan username" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="input" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:6px;">Masuk ke Dashboard</button>
        </form>

        <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;font-size:.85rem;">
            <a href="<?= e(url('index.php')) ?>" style="color:var(--primary);font-weight:600;">&larr; Kembali ke Website</a>
            <span style="color:var(--text-light);">Default: <code>admin</code> / <code>admin123</code></span>
        </div>
    </div>
</body>
</html>
