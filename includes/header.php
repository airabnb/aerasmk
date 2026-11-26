<?php
if (!isset($pageTitle)) {
    $pageTitle = SITE_NAME;
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — <?= e(SITE_NAME) ?></title>
    <meta name="description" content="<?= e(SITE_TAGLINE) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body>
<header class="navbar">
    <div class="container navbar-inner">
        <a href="<?= e(url('index.php')) ?>" class="brand">
            <div class="brand-logo" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
            </div>
            <div class="brand-text">
                <strong>SMK Bangun Nusa Bangsa</strong>
                <span>Sekolah Pusat Keunggulan</span>
            </div>
        </a>

        <nav class="nav-links" id="navMenu" aria-label="Navigasi Utama">
            <a href="<?= e(url('index.php')) ?>" class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">Beranda</a>
            <a href="<?= e(url('jurusan.php')) ?>" class="nav-link <?= in_array($currentPage, ['jurusan.php', 'jurusan-detail.php']) ? 'active' : '' ?>">Jurusan</a>
            <a href="<?= e(url('informasi.php')) ?>" class="nav-link <?= $currentPage === 'informasi.php' ? 'active' : '' ?>">Informasi</a>
            <a href="<?= e(url('artikel.php')) ?>" class="nav-link <?= in_array($currentPage, ['artikel.php', 'artikel-detail.php']) ? 'active' : '' ?>">Artikel</a>
        </nav>

        <div class="nav-actions">
            <a href="<?= e(url('informasi.php#ppdb')) ?>" class="btn btn-outline btn-sm">Info PPDB</a>
            <?php if (isLoggedIn()): ?>
                <a href="<?= e(url('admin/index.php')) ?>" class="btn btn-primary btn-sm">Dashboard Admin</a>
            <?php else: ?>
                <a href="<?= e(url('admin/login.php')) ?>" class="btn btn-primary btn-sm">Login Admin</a>
            <?php endif; ?>
            <button type="button" class="hamburger" id="navToggle" aria-label="Buka Menu" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
        </div>
    </div>
</header>
<main>
