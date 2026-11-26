<?php
/**
 * Helper Functions
 * SMK Bangun Nusa Bangsa
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Escape output HTML untuk mencegah XSS
 */
function e(?string $str): string
{
    return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate URL relatif terhadap BASE_URL
 */
function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return BASE_URL . ($path !== '' ? '/' . $path : '/');
}

/**
 * Redirect helper
 */
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

/**
 * Flash message helper
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}

function getFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * CSRF Token Helpers
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function verifyCsrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return $token !== '' && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * Membuat slug URL yang rapi dari judul
 */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'item-' . time();
}

/**
 * Format tanggal Indonesia
 */
function formatTanggal(?string $dateStr): string
{
    if (!$dateStr) {
        return '-';
    }
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $ts = strtotime($dateStr);
    if (!$ts) {
        return $dateStr;
    }
    $d = date('j', $ts);
    $m = $bulan[(int) date('n', $ts)] ?? date('M', $ts);
    $y = date('Y', $ts);
    return "$d $m $y";
}

/**
 * Potong teks dengan rapi
 */
function excerpt(string $text, int $limit = 140): string
{
    $clean = trim(strip_tags($text));
    if (mb_strlen($clean) <= $limit) {
        return $clean;
    }
    return rtrim(mb_substr($clean, 0, $limit)) . '...';
}

/**
 * Upload gambar ke folder assets/uploads/
 * Mengembalikan nama file relatif atau null jika tidak ada file yang diupload
 */
function uploadImage(string $inputName, string $prefix = 'img', ?string &$error = null): ?string
{
    if (empty($_FILES[$inputName]) || $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$inputName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Terjadi kesalahan saat mengunggah file.';
        return null;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        $error = 'Ukuran gambar maksimal 2 MB.';
        return null;
    }

    $allowedMime = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset($allowedMime[$mime])) {
        $error = 'Format gambar harus JPG, PNG, WEBP, atau GIF.';
        return null;
    }

    $uploadDir = __DIR__ . '/../assets/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = $prefix . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowedMime[$mime];
    $dest = $uploadDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        $error = 'Gagal menyimpan file gambar ke server.';
        return null;
    }

    return 'assets/uploads/' . $filename;
}

/**
 * Hapus file gambar lama jika ada
 */
function deleteUploadedFile(?string $relativePath): void
{
    if (!$relativePath) {
        return;
    }
    if (strpos($relativePath, 'assets/uploads/') === 0) {
        $fullPath = __DIR__ . '/../' . $relativePath;
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}

/**
 * Cek autentikasi admin
 */
function isLoggedIn(): bool
{
    return !empty($_SESSION['admin_user']);
}

function requireAuth(): void
{
    if (!isLoggedIn()) {
        setFlash('warning', 'Silakan login terlebih dahulu untuk mengakses halaman admin.');
        redirect('admin/login.php');
    }
}

/**
 * SVG icon helper untuk jurusan
 */
function getJurusanIcon(string $icon): string
{
    $icons = [
        'code' => '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
        'network' => '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="16" y="16" width="6" height="6" rx="1"/><rect x="2" y="16" width="6" height="6" rx="1"/><rect x="9" y="2" width="6" height="6" rx="1"/><path d="M5 16v-3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3"/><path d="M12 12V8"/></svg>',
        'palette' => '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/></svg>',
        'calculator' => '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><line x1="16" x2="16" y1="14" y2="18"/><path d="M16 10h.01"/><path d="M12 10h.01"/><path d="M8 10h.01"/><path d="M12 14h.01"/><path d="M8 14h.01"/><path d="M12 18h.01"/><path d="M8 18h.01"/></svg>',
        'cpu' => '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="16" x="4" y="4" rx="2"/><rect width="6" height="6" x="9" y="9" rx="1"/><path d="M15 2v2"/><path d="M15 20v2"/><path d="M2 15h2"/><path d="M2 9h2"/><path d="M20 15h2"/><path d="M20 9h2"/><path d="M9 2v2"/><path d="M9 20v2"/></svg>',
        'wrench' => '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
    ];
    return $icons[$icon] ?? $icons['cpu'];
}
