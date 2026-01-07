<?php
session_start();
require_once '../config/database.php';

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil dan bersihkan input
    $username = clean_input($_POST['username']);
    $password = $_POST['password']; // Password tidak perlu di-escape, akan di-verify
    
    // Validasi input
    if (empty($username) || empty($password)) {
        set_flash_message('danger', 'Username dan password harus diisi');
        redirect('login.php');
    }
    
    // Query untuk cek user
    $query = "SELECT u.*, r.nama_role, s.nama_seksi 
              FROM users u 
              LEFT JOIN roles r ON u.role_id = r.id 
              LEFT JOIN seksi s ON u.seksi_id = s.id 
              WHERE u.username = ? AND u.status = 'aktif'";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // Cek apakah user ditemukan
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['nama_role'] = $user['nama_role'];
            $_SESSION['seksi_id'] = $user['seksi_id'];
            $_SESSION['nama_seksi'] = $user['nama_seksi'];
            
            // Redirect sesuai role
            switch ($user['role_id']) {
                case 1: // Admin
                    set_flash_message('success', 'Selamat datang, ' . $user['nama_lengkap']);
                    redirect('../admin/dashboard.php');
                    break;
                    
                case 2: // Pimpinan
                    set_flash_message('success', 'Selamat datang, ' . $user['nama_lengkap']);
                    redirect('../pimpinan/dashboard.php');
                    break;
                    
                case 3: // User
                    set_flash_message('success', 'Selamat datang, ' . $user['nama_lengkap']);
                    redirect('../user/dashboard.php');
                    break;
                    
                default:
                    set_flash_message('danger', 'Role tidak valid');
                    redirect('login.php');
            }
            
        } else {
            set_flash_message('danger', 'Password salah');
            redirect('login.php');
        }
        
    } else {
        set_flash_message('danger', 'Username tidak ditemukan atau akun tidak aktif');
        redirect('login.php');
    }
    
} else {
    redirect('login.php');
}
?>