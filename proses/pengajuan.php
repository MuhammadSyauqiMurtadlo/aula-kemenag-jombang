<?php
session_start();
require_once '../config/database.php';

check_role([3]); // Hanya User

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = clean_input($_POST['action']);
    
    if ($action == 'add') {
        // Ambil dan validasi input
        $nama_pemohon = clean_input($_POST['nama_pemohon']);
        $seksi_id = (int)$_POST['seksi_id'];
        $aula_id = (int)$_POST['aula_id'];
        $nama_kegiatan = clean_input($_POST['nama_kegiatan']);
        $tanggal_mulai = clean_input($_POST['tanggal_mulai']);
        $tanggal_selesai = clean_input($_POST['tanggal_selesai']);
        $waktu_mulai = clean_input($_POST['waktu_mulai']);
        $waktu_selesai = clean_input($_POST['waktu_selesai']);
        $jumlah_peserta = !empty($_POST['jumlah_peserta']) ? (int)$_POST['jumlah_peserta'] : null;
        $keterangan = clean_input($_POST['keterangan']);
        $user_id = $_SESSION['user_id'];
        
        // Validasi input
        if (empty($nama_pemohon) || empty($seksi_id) || empty($aula_id) || empty($nama_kegiatan) || 
            empty($tanggal_mulai) || empty($tanggal_selesai) || empty($waktu_mulai) || empty($waktu_selesai)) {
            set_flash_message('danger', 'Semua field wajib harus diisi');
            redirect('../user/pengajuan.php');
        }
        
        // Validasi tanggal
        if ($tanggal_selesai < $tanggal_mulai) {
            set_flash_message('danger', 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai');
            redirect('../user/pengajuan.php');
        }
        
        // Validasi waktu
        if ($waktu_selesai <= $waktu_mulai) {
            set_flash_message('danger', 'Waktu selesai harus lebih besar dari waktu mulai');
            redirect('../user/pengajuan.php');
        }
        
        // Validasi tanggal tidak boleh di masa lalu
        if ($tanggal_mulai < date('Y-m-d')) {
            set_flash_message('danger', 'Tanggal mulai tidak boleh di masa lalu');
            redirect('../user/pengajuan.php');
        }
        
        // Cek bentrok jadwal
        if (cek_bentrok_jadwal($aula_id, $tanggal_mulai, $tanggal_selesai, $waktu_mulai, $waktu_selesai)) {
            set_flash_message('danger', 'Jadwal aula bentrok dengan pengajuan lain yang sudah disetujui atau menunggu persetujuan');
            redirect('../user/pengajuan.php');
        }
        
        // Generate kode pengajuan
        $kode_pengajuan = generate_kode_pengajuan();
        
        // Cek apakah kode sudah ada (sangat jarang terjadi)
        $check = mysqli_query($conn, "SELECT id FROM pengajuan_aula WHERE kode_pengajuan = '$kode_pengajuan'");
        while (mysqli_num_rows($check) > 0) {
            $kode_pengajuan = generate_kode_pengajuan();
            $check = mysqli_query($conn, "SELECT id FROM pengajuan_aula WHERE kode_pengajuan = '$kode_pengajuan'");
        }
        
        // Insert pengajuan dengan status "Menunggu Persetujuan" (status_id = 1)
        $query = "INSERT INTO pengajuan_aula 
                  (kode_pengajuan, user_id, seksi_id, aula_id, nama_pemohon, tanggal_mulai, tanggal_selesai, 
                   waktu_mulai, waktu_selesai, nama_kegiatan, jumlah_peserta, keterangan, status_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "siiissssssis", 
            $kode_pengajuan, $user_id, $seksi_id, $aula_id, $nama_pemohon, 
            $tanggal_mulai, $tanggal_selesai, $waktu_mulai, $waktu_selesai, 
            $nama_kegiatan, $jumlah_peserta, $keterangan
        );
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', "Pengajuan berhasil diajukan dengan kode: <strong>$kode_pengajuan</strong>. Menunggu persetujuan admin.");
            redirect('../user/status.php');
        } else {
            set_flash_message('danger', 'Gagal mengajukan peminjaman aula');
            redirect('../user/pengajuan.php');
        }
        
    } elseif ($action == 'cancel') {
        // Batalkan pengajuan
        $id = (int)$_POST['id'];
        
        // Cek apakah pengajuan milik user yang login
        $check = mysqli_query($conn, "SELECT * FROM pengajuan_aula WHERE id = $id AND user_id = {$_SESSION['user_id']}");
        
        if (mysqli_num_rows($check) == 0) {
            set_flash_message('danger', 'Pengajuan tidak ditemukan');
            redirect('../user/status.php');
        }
        
        $data = mysqli_fetch_assoc($check);
        
        // Hanya bisa membatalkan jika status masih "Menunggu Persetujuan"
        if ($data['status_id'] != 1) {
            set_flash_message('danger', 'Pengajuan tidak dapat dibatalkan karena sudah diproses');
            redirect('../user/status.php');
        }
        
        // Update status menjadi "Dibatalkan" (status_id = 4)
        $query = "UPDATE pengajuan_aula SET status_id = 4 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Pengajuan berhasil dibatalkan');
        } else {
            set_flash_message('danger', 'Gagal membatalkan pengajuan');
        }
        
        redirect('../user/status.php');
    }
    
} else {
    redirect('../user/pengajuan.php');
}
?>