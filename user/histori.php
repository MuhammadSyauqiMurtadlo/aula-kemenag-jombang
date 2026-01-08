<?php
session_start();
require_once '../config/database.php';

check_role([3]);
$page_title = 'Histori Seksi';

$seksi_id = $_SESSION['seksi_id'];

// Ambil histori seksi sendiri
$query = "SELECT h.*, a.nama_aula, p.kode_pengajuan, p.nama_pemohon
          FROM histori_penggunaan h
          LEFT JOIN aula a ON h.aula_id = a.id
          LEFT JOIN pengajuan_aula p ON h.pengajuan_id = p.id
          WHERE h.seksi_id = $seksi_id
          ORDER BY h.created_at DESC";

$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Histori Penggunaan Aula - <?php echo $_SESSION['nama_seksi']; ?></h3>
    </div>

    <div class="card-body">
        <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="alert alert-info">Belum ada histori penggunaan untuk seksi Anda</div>
        <?php else: ?>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Pemohon</th>
                        <th>Kegiatan</th>
                        <th>Aula</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Peserta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['kode_pengajuan']; ?></td>
                        <td><?php echo $row['nama_pemohon']; ?></td>
                        <td><?php echo $row['nama_kegiatan']; ?></td>
                        <td><?php echo $row['nama_aula']; ?></td>
                        <td><?php echo format_tanggal($row['tanggal_mulai']); ?></td>
                        <td><?php echo substr($row['waktu_mulai'], 0, 5); ?> - <?php echo substr($row['waktu_selesai'], 0, 5); ?></td>
                        <td><?php echo $row['jumlah_peserta'] ?? '-'; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>