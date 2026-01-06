<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode([]);
    exit;
}

$aula_id = isset($_GET['aula_id']) ? (int)$_GET['aula_id'] : 0;
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? clean_input($_GET['tanggal_mulai']) : '';
$tanggal_selesai = isset($_GET['tanggal_selesai']) ? clean_input($_GET['tanggal_selesai']) : '';

if (empty($aula_id) || empty($tanggal_mulai) || empty($tanggal_selesai)) {
    echo json_encode([]);
    exit;
}

// Query untuk mendapatkan jadwal yang bentrok
$query = "SELECT p.nama_kegiatan, p.tanggal_mulai, p.tanggal_selesai, 
          p.waktu_mulai, p.waktu_selesai, s.nama_seksi
          FROM pengajuan_aula p
          LEFT JOIN seksi s ON p.seksi_id = s.id
          WHERE p.aula_id = ? 
          AND p.status_id IN (1, 2) 
          AND (
              (p.tanggal_mulai <= ? AND p.tanggal_selesai >= ?)
              OR (p.tanggal_mulai <= ? AND p.tanggal_selesai >= ?)
              OR (p.tanggal_mulai >= ? AND p.tanggal_selesai <= ?)
          )
          ORDER BY p.tanggal_mulai, p.waktu_mulai";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "issssss", 
    $aula_id, 
    $tanggal_mulai, $tanggal_mulai,
    $tanggal_selesai, $tanggal_selesai,
    $tanggal_mulai, $tanggal_selesai
);

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$jadwal = [];
while ($row = mysqli_fetch_assoc($result)) {
    $jadwal[] = [
        'nama_kegiatan' => $row['nama_kegiatan'],
        'nama_seksi' => $row['nama_seksi'],
        'tanggal_mulai' => format_tanggal($row['tanggal_mulai']),
        'tanggal_selesai' => format_tanggal($row['tanggal_selesai']),
        'waktu_mulai' => substr($row['waktu_mulai'], 0, 5),
        'waktu_selesai' => substr($row['waktu_selesai'], 0, 5)
    ];
}

echo json_encode($jadwal);
?>