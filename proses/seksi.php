<?php
session_start();
require_once '../config/database.php';

check_role([1]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = clean_input($_POST['action']);
    
    switch ($action) {
        case 'add':
            $nama_seksi = clean_input($_POST['nama_seksi']);
            $keterangan = clean_input($_POST['keterangan']);
            
            if (empty($nama_seksi)) {
                set_flash_message('danger', 'Nama seksi harus diisi');
                redirect('../admin/seksi.php');
            }
            
            $check = mysqli_query($conn, "SELECT id FROM seksi WHERE nama_seksi = '$nama_seksi'");
            if (mysqli_num_rows($check) > 0) {
                set_flash_message('danger', 'Nama seksi sudah ada');
                redirect('../admin/seksi.php');
            }
            
            $query = "INSERT INTO seksi (nama_seksi, keterangan) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ss", $nama_seksi, $keterangan);
            
            if (mysqli_stmt_execute($stmt)) {
                set_flash_message('success', 'Seksi berhasil ditambahkan');
            } else {
                set_flash_message('danger', 'Gagal menambahkan seksi');
            }
            
            redirect('../admin/seksi.php');
            break;
            
        case 'edit':
            $id = (int)$_POST['id'];
            $nama_seksi = clean_input($_POST['nama_seksi']);
            $keterangan = clean_input($_POST['keterangan']);
            
            if (empty($nama_seksi)) {
                set_flash_message('danger', 'Nama seksi harus diisi');
                redirect('../admin/seksi.php');
            }
            
            $check = mysqli_query($conn, "SELECT id FROM seksi WHERE nama_seksi = '$nama_seksi' AND id != $id");
            if (mysqli_num_rows($check) > 0) {
                set_flash_message('danger', 'Nama seksi sudah ada');
                redirect('../admin/seksi.php');
            }
            
            $query = "UPDATE seksi SET nama_seksi = ?, keterangan = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssi", $nama_seksi, $keterangan, $id);
            
            if (mysqli_stmt_execute($stmt)) {
                set_flash_message('success', 'Seksi berhasil diupdate');
            } else {
                set_flash_message('danger', 'Gagal mengupdate seksi');
            }
            
            redirect('../admin/seksi.php');
            break;
            
        case 'delete':
            $id = (int)$_POST['id'];
            
            $check = mysqli_query($conn, "SELECT id FROM users WHERE seksi_id = $id");
            if (mysqli_num_rows($check) > 0) {
                set_flash_message('danger', 'Seksi tidak bisa dihapus karena masih ada user terkait');
                redirect('../admin/seksi.php');
            }
            
            $query = "DELETE FROM seksi WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $id);
            
            if (mysqli_stmt_execute($stmt)) {
                set_flash_message('success', 'Seksi berhasil dihapus');
            } else {
                set_flash_message('danger', 'Gagal menghapus seksi');
            }
            
            redirect('../admin/seksi.php');
            break;
            
        default:
            redirect('../admin/seksi.php');
    }
} else {
    redirect('../admin/seksi.php');
}
?>