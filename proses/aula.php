<?php
session_start();
require_once '../config/database.php';

// Cek role admin
check_role([1]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = clean_input($_POST['action']);
    
    switch ($action) {
        case 'add':
            // Tambah aula baru
            $nama_aula = clean_input($_POST['nama_aula']);
            $kapasitas = !empty($_POST['kapasitas']) ? (int)$_POST['kapasitas'] : null;
            $fasilitas = clean_input($_POST['fasilitas']);
            $keterangan = clean_input($_POST['keterangan']);
            $status = clean_input($_POST['status']);
            
            // Validasi
            if (empty($nama_aula)) {
                set_flash_message('danger', 'Nama aula harus diisi');
                redirect('../admin/aula.php');
            }
            
            // Cek duplikat nama
            $check = mysqli_query($conn, "SELECT id FROM aula WHERE nama_aula = '$nama_aula'");
            if (mysqli_num_rows($check) > 0) {
                set_flash_message('danger', 'Nama aula sudah ada');
                redirect('../admin/aula.php');
            }
            
            // Insert
            $query = "INSERT INTO aula (nama_aula, kapasitas, fasilitas, keterangan, status) 
                      VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sisss", $nama_aula, $kapasitas, $fasilitas, $keterangan, $status);
            
            if (mysqli_stmt_execute($stmt)) {
                set_flash_message('success', 'Aula berhasil ditambahkan');
            } else {
                set_flash_message('danger', 'Gagal menambahkan aula');
            }
            
            redirect('../admin/aula.php');
            break;
            
        case 'edit':
            // Edit aula
            $id = (int)$_POST['id'];
            $nama_aula = clean_input($_POST['nama_aula']);
            $kapasitas = !empty($_POST['kapasitas']) ? (int)$_POST['kapasitas'] : null;
            $fasilitas = clean_input($_POST['fasilitas']);
            $keterangan = clean_input($_POST['keterangan']);
            $status = clean_input($_POST['status']);
            
            // Validasi
            if (empty($nama_aula)) {
                set_flash_message('danger', 'Nama aula harus diisi');
                redirect('../admin/aula.php');
            }
            
            // Cek duplikat nama (kecuali untuk id yang sama)
            $check = mysqli_query($conn, "SELECT id FROM aula WHERE nama_aula = '$nama_aula' AND id != $id");
            if (mysqli_num_rows($check) > 0) {
                set_flash_message('danger', 'Nama aula sudah ada');
                redirect('../admin/aula.php');
            }
            
            // Update
            $query = "UPDATE aula SET 
                      nama_aula = ?, 
                      kapasitas = ?, 
                      fasilitas = ?, 
                      keterangan = ?, 
                      status = ? 
                      WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sisssi", $nama_aula, $kapasitas, $fasilitas, $keterangan, $status, $id);
            
            if (mysqli_stmt_execute($stmt)) {
                set_flash_message('success', 'Aula berhasil diupdate');
            } else {
                set_flash_message('danger', 'Gagal mengupdate aula');
            }
            
            redirect('../admin/aula.php');
            break;
            
        case 'delete':
            // Hapus aula
            $id = (int)$_POST['id'];
            
            // Cek apakah aula sedang digunakan
            $check = mysqli_query($conn, "SELECT id FROM pengajuan_aula WHERE aula_id = $id AND status_id IN (1, 2)");
            if (mysqli_num_rows($check) > 0) {
                set_flash_message('danger', 'Aula tidak bisa dihapus karena masih ada pengajuan aktif');
                redirect('../admin/aula.php');
            }
            
            // Delete
            $query = "DELETE FROM aula WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $id);
            
            if (mysqli_stmt_execute($stmt)) {
                set_flash_message('success', 'Aula berhasil dihapus');
            } else {
                set_flash_message('danger', 'Gagal menghapus aula');
            }
            
            redirect('../admin/aula.php');
            break;
            
        default:
            redirect('../admin/aula.php');
    }
} else {
    redirect('../admin/aula.php');
}
?>