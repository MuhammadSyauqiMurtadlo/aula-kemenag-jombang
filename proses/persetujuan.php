<?php
session_start();
require_once '../config/database.php';

check_role([1]); // Hanya Admin

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = clean_input($_POST['action']);
    $id = (int)$_POST['id'];
    $admin_id = $_SESSION['user_id'];
    
    // Cek apakah pengajuan ada
    $check = mysqli_query($conn, "SELECT * FROM pengajuan_aula WHERE id = $id");
    
    if (mysqli_num_rows($check) == 0) {
        set_flash_message('danger', 'Pengajuan tidak ditemukan');
        redirect('../admin/persetujuan.php');
    }
    
    $data = mysqli_fetch_assoc($check);
    
    // Cek apakah masih menunggu persetujuan
    if ($data['status_id'] != 1) {
        set_flash_message('danger', 'Pengajuan sudah diproses sebelumnya');
        redirect('../admin/persetujuan.php');
    }
    
    if ($action == 'setujui') {
        // Cek bentrok jadwal lagi (untuk memastikan)
        if (cek_bentrok_jadwal($data['aula_id'], $data['tanggal_mulai'], $data['tanggal_selesai'], 
                                $data['waktu_mulai'], $data['waktu_selesai'], $id)) {
            set_flash_message('danger', 'Jadwal aula bentrok dengan pengajuan lain yang sudah disetujui');
            redirect('../admin/persetujuan.php');
        }
        
        // Update status menjadi "Disetujui" (status_id = 2)
        $query = "UPDATE pengajuan_aula 
                  SET status_id = 2, 
                      disetujui_oleh = ?, 
                      tanggal_persetujuan = NOW() 
                  WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ii", $admin_id, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Insert ke histori penggunaan
            $insert_histori = "INSERT INTO histori_penggunaan 
                               (pengajuan_id, aula_id, seksi_id, nama_kegiatan, tanggal_mulai, 
                                tanggal_selesai, waktu_mulai, waktu_selesai, jumlah_peserta, keterangan)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt2 = mysqli_prepare($conn, $insert_histori);
            mysqli_stmt_bind_param($stmt2, "iiisssssis", 
            $id, $data['aula_id'], $data['seksi_id'], $data['nama_kegiatan'],
            $data['tanggal_mulai'], $data['tanggal_selesai'], 
            $data['waktu_mulai'], $data['waktu_selesai'], 
            $data['jumlah_peserta'], $data['keterangan']
        );
            
            mysqli_stmt_execute($stmt2);
            
            set_flash_message('success', 'Pengajuan berhasil disetujui');
        } else {
            set_flash_message('danger', 'Gagal menyetujui pengajuan');
        }
        
        redirect('../admin/persetujuan.php');
        
    } elseif ($action == 'tolak') {
        $alasan_penolakan = clean_input($_POST['alasan_penolakan']);
        
        if (empty($alasan_penolakan)) {
            set_flash_message('danger', 'Alasan penolakan harus diisi');
            redirect('../admin/persetujuan.php');
        }
        
        // Update status menjadi "Ditolak" (status_id = 3)
        $query = "UPDATE pengajuan_aula 
                  SET status_id = 3, 
                      alasan_penolakan = ?, 
                      disetujui_oleh = ?, 
                      tanggal_persetujuan = NOW() 
                  WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sii", $alasan_penolakan, $admin_id, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            set_flash_message('success', 'Pengajuan berhasil ditolak');
        } else {
            set_flash_message('danger', 'Gagal menolak pengajuan');
        }
        
        redirect('../admin/persetujuan.php');
    }
    
} else {
    redirect('../admin/persetujuan.php');
}
?>