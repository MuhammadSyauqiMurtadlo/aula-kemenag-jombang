<?php
session_start();
require_once '../config/database.php';

// Jika sudah login, redirect ke dashboard sesuai role
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

// Ambil jadwal aula yang terpakai (7 hari ke depan)
$today = date('Y-m-d');
$next_week = date('Y-m-d', strtotime('+7 days'));

$query_jadwal = "SELECT p.*, a.nama_aula, s.nama_seksi
                 FROM pengajuan_aula p
                 LEFT JOIN aula a ON p.aula_id = a.id
                 LEFT JOIN seksi s ON p.seksi_id = s.id
                 WHERE p.status_id IN (1, 2)
                 ORDER BY p.tanggal_mulai, p.waktu_mulai";

$jadwal_result = mysqli_query($conn, $query_jadwal);

$flash = get_flash_message();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Peminjaman Aula Kemenag Jombang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e22ce 100%); */
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            background: #1e3c72;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 1100px;
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .login-section {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header img {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }

        .login-header h1 {
            font-size: 24px;
            color: #1e3c72;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        .badge-sistem {
            display: inline-block;
            background: #1e3c72;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            margin-top: 5px;
            font-weight: 500;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #1e3c72;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        }

        .login-footer {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #666;
        }

        .login-footer strong {
            color: #1e3c72;
        }

        .login-footer ul {
            list-style: none;
            margin-top: 10px;
            padding-left: 0;
        }

        .login-footer li {
            margin-bottom: 5px;
            padding-left: 20px;
            position: relative;
        }

        .login-footer li:before {
            content: "→";
            position: absolute;
            left: 0;
            color: #1e3c72;
        }

        /* Jadwal Section */
        .jadwal-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 40px;
            border-left: 1px solid #e0e0e0;
            overflow-y: auto;
            max-height: 600px;
        }

        .jadwal-header {
            margin-bottom: 25px;
        }

        .jadwal-header h2 {
            font-size: 20px;
            color: #1e3c72;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .jadwal-header p {
            color: #666;
            font-size: 13px;
        }

        .jadwal-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            border-left: 4px solid #1e3c72;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .jadwal-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .jadwal-card h3 {
            font-size: 15px;
            color: #1e3c72;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .jadwal-info {
            display: grid;
            gap: 6px;
            font-size: 13px;
            color: #555;
        }

        .jadwal-info-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .jadwal-icon {
            width: 16px;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-wrapper {
                grid-template-columns: 1fr;
            }

            .jadwal-section {
                border-left: none;
                border-top: 1px solid #e0e0e0;
                max-height: 400px;
            }

            .login-section {
                padding: 30px 20px;
            }

            .jadwal-section {
                padding: 30px 20px;
            }
        }

        /* Scrollbar styling */
        .jadwal-section::-webkit-scrollbar {
            width: 6px;
        }

        .jadwal-section::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .jadwal-section::-webkit-scrollbar-thumb {
            background: #1e3c72;
            border-radius: 3px;
        }

        .jadwal-section::-webkit-scrollbar-thumb:hover {
            background: #2a5298;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Login Section -->
        <div class="login-section">
            <div class="login-header">
                <img src="../assets/img/kemenag.png" alt="Logo Kemenag" onerror="this.style.display='none'">
                <h1>Sistem Peminjaman Aula</h1>
                <p>Kementerian Agama Kabupaten Jombang</p>
                <!-- <span class="badge-sistem">APLIKASI RESMI</span> -->
            </div>

            <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
            <?php endif; ?>

            <form action="proses_login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Masukkan username" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="btn-login">🔐 Login</button>
            </form>

            <div class="login-footer">
                <!-- <p><strong>Akun Default untuk Testing:</strong></p>
                <ul>
                    <li><strong>Admin:</strong> admin / admin123</li>
                    <li><strong>Pimpinan:</strong> pimpinan / pimpinan123</li>
                    <li><strong>User:</strong> user_keuangan / user123</li>
                </ul> -->
                <p style="margin-top: 15px; color: #999; font-size: 11px;">
                    © <?php echo date('Y'); ?> Kementerian Agama Kab. Jombang
                </p>
            </div>
        </div>

        <!-- Jadwal Section -->
        <div class="jadwal-section">
            <div class="jadwal-header">
                <h2>📅 Jadwal Aula</h2>
                <p>Lihat jadwal peminjaman aula yang sudah terjadwal</p>
            </div>

            <?php if (mysqli_num_rows($jadwal_result) == 0): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <p>Tidak ada jadwal aula untuk 7 hari ke depan</p>
            </div>
            <?php else: ?>
                <?php while ($jadwal = mysqli_fetch_assoc($jadwal_result)): ?>
                <div class="jadwal-card">
                    <h3><?php echo $jadwal['nama_kegiatan']; ?></h3>
                    <div class="jadwal-info">
                        <div class="jadwal-info-row">
                            <span class="jadwal-icon">🏢</span>
                            <strong><?php echo $jadwal['nama_aula']; ?></strong>
                        </div>
                        <div class="jadwal-info-row">
                            <span class="jadwal-icon">📁</span>
                            <?php echo $jadwal['nama_seksi']; ?>
                        </div>
                        <div class="jadwal-info-row">
                            <span class="jadwal-icon">📅</span>
                            <?php echo format_tanggal($jadwal['tanggal_mulai']); ?>
                            <?php if ($jadwal['tanggal_mulai'] != $jadwal['tanggal_selesai']): ?>
                            - <?php echo format_tanggal($jadwal['tanggal_selesai']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="jadwal-info-row">
                            <span class="jadwal-icon">🕐</span>
                            <?php echo substr($jadwal['waktu_mulai'], 0, 5); ?> - 
                            <?php echo substr($jadwal['waktu_selesai'], 0, 5); ?> WIB
                        </div>
                        <div class="jadwal-info-row">
                            <span class="jadwal-icon">📋</span>
                            <span class="badge badge-<?php echo $jadwal['status_id'] == 2 ? 'success' : 'warning'; ?>">
                                <?php echo $jadwal['status_id'] == 2 ? 'Disetujui' : 'Menunggu Persetujuan'; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>