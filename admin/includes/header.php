<?php
requireAuth();
if (!isset($pageTitle)) {
    $pageTitle = 'Dashboard Admin';
}
$currentAdminPage = basename($_SERVER['PHP_SELF']);
$adminUser = $_SESSION['admin_user'];
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — Admin <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
    <style>
        .admin-shell { display: flex; min-height: 100vh; background: var(--bg); }
        .admin-sidebar {
            width: 260px;
            background: var(--navy);
            color: #cbd5e1;
            display: flex;
            flex-direction: column;
            padding: 24px 18px;
            position: sticky;
            top: 0;
            height: 100vh;
            flex-shrink: 0;
        }
        .admin-nav { display: grid; gap: 6px; margin-top: 28px; }
        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: .92rem;
            color: #cbd5e1;
            transition: var(--transition);
        }
        .admin-nav-link:hover, .admin-nav-link.active {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }
        .admin-nav-link.active {
            background: var(--primary);
        }
        .admin-main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .admin-topbar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .admin-content { padding: 28px; max-width: 1200px; width: 100%; }
        .table-wrap {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            overflow-x: auto;
            box-shadow: var(--shadow-sm);
        }
        table { width: 100%; border-collapse: collapse; font-size: .92rem; }
        th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid var(--border); vertical-align: middle; }
        th { background: var(--surface-alt); font-family: 'Plus Jakarta Sans', sans-serif; font-size: .82rem; text-transform: uppercase; letter-spacing: .04em; color: var(--text-muted); }
        tr:last-child td { border-bottom: none; }
        .form-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 28px; box-shadow: var(--shadow-sm); }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: .88rem; font-weight: 600; color: var(--navy); margin-bottom: 6px; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #b91c1c; }
        .mobile-admin-toggle { display: none; }
        @media (max-width: 900px) {
            .admin-sidebar {
                position: fixed;
                left: -280px;
                z-index: 200;
                transition: left 0.25s ease;
            }
            .admin-sidebar.open { left: 0; }
            .mobile-admin-toggle { display: inline-flex; }
            .admin-content { padding: 18px; }
        }
    </style>
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="<?= e(url('admin/index.php')) ?>" class="brand">
            <div class="brand-logo" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
            </div>
            <div class="brand-text">
                <strong style="color:#fff;">SMK BNB Admin</strong>
                <span style="color:#93c5fd;">Panel Pengelola</span>
            </div>
        </a>

        <nav class="admin-nav">
            <a href="<?= e(url('admin/index.php')) ?>" class="admin-nav-link <?= $currentAdminPage === 'index.php' ? 'active' : '' ?>">Dashboard</a>
            <a href="<?= e(url('admin/artikel.php')) ?>" class="admin-nav-link <?= strpos($currentAdminPage, 'artikel') === 0 ? 'active' : '' ?>">Kelola Artikel</a>
            <a href="<?= e(url('admin/jurusan.php')) ?>" class="admin-nav-link <?= strpos($currentAdminPage, 'jurusan') === 0 ? 'active' : '' ?>">Kelola Jurusan</a>
            <a href="<?= e(url('admin/informasi.php')) ?>" class="admin-nav-link <?= strpos($currentAdminPage, 'informasi') === 0 ? 'active' : '' ?>">Kelola Informasi</a>
        </nav>

        <div style="margin-top:auto;padding-top:20px;border-top:1px solid rgba(255,255,255,.1);display:grid;gap:8px;">
            <a href="<?= e(url('index.php')) ?>" class="admin-nav-link" target="_blank">Lihat Website &nearr;</a>
            <a href="<?= e(url('admin/logout.php')) ?>" class="admin-nav-link" style="color:#fca5a5;">Keluar</a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                <button type="button" class="btn btn-outline btn-sm mobile-admin-toggle" onclick="document.getElementById('adminSidebar').classList.toggle('open')">
                    Menu
                </button>
                <strong style="font-family:'Plus Jakarta Sans',sans-serif;font-size:1.05rem;"><?= e($pageTitle) ?></strong>
            </div>
            <div style="font-size:.88rem;color:var(--text-muted);">
                Masuk sebagai <strong style="color:var(--navy);"><?= e($adminUser['nama']) ?></strong>
            </div>
        </header>

        <div class="admin-content">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>" data-auto-dismiss="true"><?= e($flash['message']) ?></div>
            <?php endif; ?>
