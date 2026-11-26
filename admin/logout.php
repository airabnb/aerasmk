<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

unset($_SESSION['admin_user']);
setFlash('success', 'Anda telah keluar dari panel admin.');
redirect('admin/login.php');
