<?php
/**
 * Konfigurasi Database & Base URL
 * SMK Bangun Nusa Bangsa
 */

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'db_smk_bnb');
define('DB_USER', 'root');
define('DB_PASS', '');

// Deteksi base URL otomatis agar berjalan baik di http://localhost/aerasmk maupun virtual host / php -S
if (!defined('BASE_URL')) {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = '';
    if (strpos($scriptName, '/aerasmk/') !== false) {
        $basePath = '/aerasmk';
    }
    define('BASE_URL', $basePath);
}

define('SITE_NAME', 'SMK Bangun Nusa Bangsa');
define('SITE_TAGLINE', 'Unggul dalam Kompetensi, Berkarakter, Siap Kerja & Berwirausaha');

/**
 * Mengembalikan instance PDO koneksi database
 */
function getDB(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        die(
            '<div style="font-family:system-ui,sans-serif;max-width:600px;margin:60px auto;padding:28px;border:1px solid #fecaca;background:#fef2f2;border-radius:12px;color:#991b1b;">'
            . '<h2 style="margin-top:0;">Koneksi Database Gagal</h2>'
            . '<p>Pastikan layanan <strong>MySQL (XAMPP)</strong> sudah berjalan dan database <code>' . htmlspecialchars(DB_NAME) . '</code> sudah dibuat.</p>'
            . '<p>Anda dapat mengimpor file <code>database.sql</code> atau menjalankan <a href="' . BASE_URL . '/setup.php" style="color:#1d4ed8;font-weight:600;">setup.php</a> untuk instalasi otomatis.</p>'
            . '<small style="color:#b91c1c;">Detail: ' . htmlspecialchars($e->getMessage()) . '</small>'
            . '</div>'
        );
    }
}
