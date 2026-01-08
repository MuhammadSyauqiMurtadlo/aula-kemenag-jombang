<?php
session_start();
require_once '../config/database.php';

// If already logged in, redirect based on role
if (is_logged_in()) {
    switch ($_SESSION['role_id']) {
        case 1:
            redirect('../admin/dashboard.php');
            break;
        case 2:
            redirect('../pimpinan/dashboard.php');
            break;
        case 3:
            redirect('../user/dashboard.php');
            break;
        
    }
}

$flash = get_flash_message();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Peminjaman Aula Kemenag Jombang</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="../assets/img/kemenag.png" alt="Logo Kemenag" class="logo" onerror="this.style.display='none'">
                <h1>Sistem Peminjaman Aula</h1>
                <p>Kementerian Agama Kabupaten Jombang</p>
            </div>

            <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
            <?php endif; ?>

            <form action="proses_login.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="login-footer">
                <!-- <p>Default Login:</p>
                <ul>
                    <li><strong>Admin:</strong> admin / admin123</li>
                    <li><strong>Pimpinan:</strong> pimpinan / pimpinan123</li>
                    <li><strong>User:</strong> user_keuangan / user123</li>
                </ul> -->
            </div>
        </div>
    </div>
</body>
</html>