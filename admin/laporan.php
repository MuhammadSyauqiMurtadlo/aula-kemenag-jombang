<?php
session_start();
require_once '../config/database.php';

check_role([1]);
$page_title = 'Laporan';

// Filter
$bulan = isset($_GET['bulan']) ? clean_input($_GET['bulan']) : date('Y-m');
$aula_id = isset($_GET['aula_id']) ? (int)$_GET['aula_id'] : 0;
$seksi_id = isset($_GET['seksi_id']) ? (int)$_GET['seksi_id'] : 0;

// Build query
$where = "p.status_id = 2";

if ($bulan) {
    $where .= " AND DATE_FORMAT(p.tanggal_mulai, '%Y-%m') = '$bulan'";
}
if ($aula_id > 0) {
    $where .= " AND p.aula_id = $aula_id";
}
if ($seksi_id > 0) {
    $where .= " AND p.seksi_id = $seksi_id";
}

$query = "SELECT p.*, a.nama_aula, s.nama_seksi, u.nama_lengkap as pemohon
          FROM pengajuan_aula p
          LEFT JOIN aula a ON p.aula_id = a.id
          LEFT JOIN seksi s ON p.seksi_id = s.id
          LEFT JOIN users u ON p.user_id = u.id
          WHERE $where
          ORDER BY p.tanggal_mulai DESC, p.waktu_mulai";

$result = mysqli_query($conn, $query);

// Data untuk filter
$aulas = mysqli_query($conn, "SELECT * FROM aula WHERE status = 'aktif' ORDER BY nama_aula");
$seksis = mysqli_query($conn, "SELECT * FROM seksi ORDER BY nama_seksi");

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Laporan Penggunaan Aula</h3>
    </div>

    <div class="card-body">
        <!-- Filter -->
        <form method="GET" class="filter-form">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Bulan</label>
                        <input type="month" name="bulan" class="form-control" value="<?php echo $bulan; ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Aula</label>
                        <select name="aula_id" class="form-control">
                            <option value="0">Semua Aula</option>
                            <?php while ($a = mysqli_fetch_assoc($aulas)): ?>
                            <option value="<?php echo $a['id']; ?>" <?php echo ($a['id'] == $aula_id) ? 'selected' : ''; ?>>
                                <?php echo $a['nama_aula']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Seksi</label>
                        <select name="seksi_id" class="form-control">
                            <option value="0">Semua Seksi</option>
                            <?php while ($s = mysqli_fetch_assoc($seksis)): ?>
                            <option value="<?php echo $s['id']; ?>" <?php echo ($s['id'] == $seksi_id) ? 'selected' : ''; ?>>
                                <?php echo $s['nama_seksi']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">🔍 Filter</button>
            <a href="laporan.php" class="btn btn-secondary">Reset</a>
            <button type="button" onclick="window.print()" class="btn btn-success">🖨️ Cetak</button>
        </form>

        <hr>

        <!-- Hasil -->
        <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="alert alert-info">Tidak ada data untuk ditampilkan</div>
        <?php else: ?>
        
        <h5>Total: <?php echo mysqli_num_rows($result); ?> penggunaan</h5>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Kegiatan</th>
                        <th>Aula</th>
                        <th>Seksi</th>
                        <th>Pemohon</th>
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
                        <td><?php echo format_tanggal($row['tanggal_mulai']); ?></td>
                        <td><?php echo substr($row['waktu_mulai'], 0, 5); ?> - <?php echo substr($row['waktu_selesai'], 0, 5); ?></td>
                        <td><?php echo $row['nama_kegiatan']; ?></td>
                        <td><?php echo $row['nama_aula']; ?></td>
                        <td><?php echo $row['nama_seksi']; ?></td>
                        <td><?php echo $row['pemohon']; ?></td>
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