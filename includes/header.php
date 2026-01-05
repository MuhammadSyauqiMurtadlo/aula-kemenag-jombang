<?php
if (!is_logged_in()) {
    redirect('../auth/login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?> - Sistem Aula Kemenag</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Aula Kemenag</h3>
                <p>Jombang</p>
            </div>

            <div class="sidebar-user">
                <div class="user-info">
                    <strong><?php echo $_SESSION['nama_lengkap']; ?></strong>
                    <span class="badge badge-<?php echo $_SESSION['role_id'] == 1 ? 'danger' : ($_SESSION['role_id'] == 2 ? 'warning' : 'primary'); ?>">
                        <?php echo $_SESSION['nama_role']; ?>
                    </span>
                    <?php if ($_SESSION['seksi_id']): ?>
                    <small><?php echo $_SESSION['nama_seksi']; ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <nav class="sidebar-menu">
                <ul>
                    <?php if ($_SESSION['role_id'] == 1): // Admin ?>
                    <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        📊 Dashboard
                    </a></li>
                    <li><a href="persetujuan.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'persetujuan.php' ? 'active' : ''; ?>">
                        ✅ Persetujuan
                    </a></li>
                    <li><a href="aula.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'aula.php' ? 'active' : ''; ?>">
                        🏢 Data Aula
                    </a></li>
                    <li><a href="seksi.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'seksi.php' ? 'active' : ''; ?>">
                        📁 Data Seksi
                    </a></li>
                    <li><a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                        👥 Data User
                    </a></li>
                    <li><a href="laporan.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : ''; ?>">
                        📄 Laporan
                    </a></li>
                    <li><a href="histori.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'histori.php' ? 'active' : ''; ?>">
                        📜 Histori
                    </a></li>
                    
                    <?php elseif ($_SESSION['role_id'] == 2): // Pimpinan ?>
                    <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        📊 Dashboard
                    </a></li>
                    <li><a href="laporan.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : ''; ?>">
                        📄 Laporan
                    </a></li>
                    <li><a href="statistik.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'statistik.php' ? 'active' : ''; ?>">
                        📈 Statistik
                    </a></li>
                    
                    <?php else: // User ?>
                    <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        📊 Dashboard
                    </a></li>
                    <li><a href="pengajuan.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'pengajuan.php' ? 'active' : ''; ?>">
                        ➕ Ajukan Peminjaman
                    </a></li>
                    <li><a href="status.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'status.php' ? 'active' : ''; ?>">
                        📋 Status Pengajuan
                    </a></li>
                    <li><a href="histori.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'histori.php' ? 'active' : ''; ?>">
                        📜 Histori Seksi
                    </a></li>
                    <?php endif; ?>
                    
                    <li><a href="../auth/logout.php" onclick="return confirm('Yakin ingin logout?')">
                        🚪 Logout
                    </a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <header class="topbar">
                <h1><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
            </header>

            <div class="content">
                <?php 
                $flash = get_flash_message();
                if ($flash): 
                ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo $flash['message']; ?>
                </div>
                <?php endif; ?>