<?php
session_start();
require_once '../config/database.php';

check_role([1]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = clean_input($_POST['action']);
    
    switch ($action) {
        case 'add':
            $username = clean_input($_POST['username']);
            $nama_lengkap = clean_input($_POST['nama_lengkap']);
            $email = clean_input($_POST['email']);
            $no_hp = clean_input($_POST['no_hp']);
            $role_id = (int)$_POST['role_id'];
            $seksi_id = !empty($_POST['seksi_id']) ? (int)$_POST['seksi_id'] : null;
            $password = $_POST['password'];
            $status = clean_input($_POST['status']);
            
            // Validasi
            if (empty($username) || empty($nama_lengkap) || empty($password)) {
                set_flash_message('danger', 'Username, nama lengkap, dan password harus diisi');
                redirect('../admin/users.php');
            }
            
            if (strlen($password) < 6) {
                set_flash_message('danger', 'Password minimal 6 karakter');
                redirect('../admin/users.php');
            }
            
            // Validasi seksi untuk role User
            if ($role_id == 3 && empty($seksi_id)) {
                set_flash_message('danger', 'Seksi harus dipilih untuk role User');
                redirect('../admin/users.php');
            }
            
            // Cek duplikat username
            $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
            if (mysqli_num_rows($check) > 0) {
                set_flash_message('danger', 'Username sudah digunakan');
                redirect('../admin/users.php');
            }
            
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert
            $query = "INSERT INTO users (username, password, nama_lengkap, email, no_hp, role_id, seksi_id, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssssiis", $username, $password_hash, $nama_lengkap, $email, $no_hp, $role_id, $seksi_id, $status);
            
            if (mysqli_stmt_execute($stmt)) {
                set_flash_message('success', 'User berhasil ditambahkan');
            } else {
                set_flash_message('danger', 'Gagal menambahkan user');
            }
            
            redirect('../admin/users.php');
            break;
            
        case 'edit':
            $id = (int)$_POST['id'];
            $username = clean_input($_POST['username']);
            $nama_lengkap = clean_input($_POST['nama_lengkap']);
            $email = clean_input($_POST['email']);
            $no_hp = clean_input($_POST['no_hp']);
            $role_id = (int)$_POST['role_id'];
            $seksi_id = !empty($_POST['seksi_id']) ? (int)$_POST['seksi_id'] : null;
            $password = $_POST['password'];
            $status = clean_input($_POST['status']);
            
            // Validasi
            if (empty($username) || empty($nama_lengkap)) {
                set_flash_message('danger', 'Username dan nama lengkap harus diisi');
                redirect('../admin/users.php');
            }
            
            // Validasi password jika diisi
            if (!empty($password) && strlen($password) < 6) {
                set_flash_message('danger', 'Password minimal 6 karakter');
                redirect('../admin/users.php');
            }
            
            // Validasi seksi untuk role User
            if ($role_id == 3 && empty($seksi_id)) {
                set_flash_message('danger', 'Seksi harus dipilih untuk role User');
                redirect('../admin/users.php');
            }
            
            // Cek duplikat username
            $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' AND id != $id");
            if (mysqli_num_rows($check) > 0) {
                set_flash_message('danger', 'Username sudah digunakan');
                redirect('../admin/users.php');
            }
            
            // Update dengan atau tanpa password
            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $query = "UPDATE users SET 
                          username = ?, 
                          password = ?, 
                          nama_lengkap = ?, 
                          email = ?, 
                          no_hp = ?, 
                          role_id = ?, 
                          seksi_id = ?, 
                          status = ? 
                          WHERE id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sssssiiSi", $username, $password_hash, $nama_lengkap, $email, $no_hp, $role_id, $seksi_id, $status, $id);
            } else {
                $query = "UPDATE users SET 
                          username = ?, 
                          nama_lengkap = ?, 
                          email = ?, 
                          no_hp = ?, 
                          role_id = ?, 
                          seksi_id = ?, 
                          status = ? 
                          WHERE id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "ssssiiSi", $username, $nama_lengkap, $email, $no_hp, $role_id, $seksi_id, $status, $id);
            }
            
            if (mysqli_stmt_execute($stmt)) {
                set_flash_message('success', 'User berhasil diupdate');
            } else {
                set_flash_message('danger', 'Gagal mengupdate user');
            }
            
            redirect('../admin/users.php');
            break;
            
        case 'delete':
            $id = (int)$_POST['id'];
            
            // Tidak bisa menghapus diri sendiri
            if ($id == $_SESSION['user_id']) {
                set_flash_message('danger', 'Tidak bisa menghapus akun sendiri');
                redirect('../admin/users.php');
            }
            
            // Cek apakah user punya pengajuan
            $check = mysqli_query($conn, "SELECT id FROM pengajuan_aula WHERE user_id = $id");
            if (mysqli_num_rows($check) > 0) {
                set_flash_message('danger', 'User tidak bisa dihapus karena memiliki riwayat pengajuan');
                redirect('../admin/users.php');
            }
            
            // Delete
            $query = "DELETE FROM users WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $id);
            
            if (mysqli_stmt_execute($stmt)) {
                set_flash_message('success', 'User berhasil dihapus');
            } else {
                set_flash_message('danger', 'Gagal menghapus user');
            }
            
            redirect('../admin/users.php');
            break;
            
        default:
            redirect('../admin/users.php');
    }
} else {
    redirect('../admin/users.php');
}
?>