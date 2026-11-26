<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf()) {
    setFlash('danger', 'Permintaan hapus tidak valid.');
    redirect('admin/informasi.php');
}

$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    $pdo = getDB();
    $del = $pdo->prepare("DELETE FROM informasi WHERE id = :id");
    $del->execute([':id' => $id]);
    setFlash('success', 'Informasi berhasil dihapus.');
}

redirect('admin/informasi.php');
