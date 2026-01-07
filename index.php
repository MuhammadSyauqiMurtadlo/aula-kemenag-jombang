<?php
session_start();

// Jika sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role_id']) {
        case 1:
            header("Location: admin/dashboard.php");
            break;
        case 2:
            header("Location: pimpinan/dashboard.php");
            break;
        case 3:
            header("Location: user/dashboard.php");
            break;
    }
    exit;
}

// Jika belum login, redirect ke halaman login
header("Location: auth/login.php");
exit;
?>