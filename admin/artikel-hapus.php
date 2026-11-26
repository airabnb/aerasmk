<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf()) {
    setFlash('danger', 'Permintaan hapus tidak valid.');
    redirect('admin/artikel.php');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT gambar FROM artikel WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    if ($row) {
        deleteUploadedFile($row['gambar'] ?? null);
        $del = $pdo->prepare("DELETE FROM artikel WHERE id = :id");
        $del->execute([':id' => $id]);
        setFlash('success', 'Artikel berhasil dihapus.');
    } else {
        setFlash('danger', 'Artikel tidak ditemukan.');
    }
}

redirect('admin/artikel.php');
