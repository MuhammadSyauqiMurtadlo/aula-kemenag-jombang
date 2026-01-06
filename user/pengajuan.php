<?php
session_start();
require_once '../config/database.php';

check_role([3]); // Hanya User
$page_title = 'Ajukan Peminjaman Aula';

// Ambil data aula aktif
$aulas = mysqli_query($conn, "SELECT * FROM aula WHERE status = 'aktif' ORDER BY nama_aula");

// Ambil data seksi
$seksis = mysqli_query($conn, "SELECT * FROM seksi ORDER BY nama_seksi");

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Form Pengajuan Peminjaman Aula</h3>
    </div>

    <div class="card-body">
        <form action="../proses/pengajuan.php" method="POST" id="pengajuanForm">
            <input type="hidden" name="action" value="add">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_pemohon">Nama Pemohon *</label>
                        <input type="text" id="nama_pemohon" name="nama_pemohon" class="form-control" 
                            value="<?php echo $_SESSION['nama_lengkap']; ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="seksi_id">Seksi *</label>
                        <select id="seksi_id" name="seksi_id" class="form-control" required>
                            <option value="">-- Pilih Seksi --</option>
                            <?php while ($s = mysqli_fetch_assoc($seksis)): ?>
                            <option value="<?php echo $s['id']; ?>" 
                                    <?php echo ($s['id'] == $_SESSION['seksi_id']) ? 'selected' : ''; ?>>
                                <?php echo $s['nama_seksi']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="aula_id">Pilih Aula *</label>
                        <select id="aula_id" name="aula_id" class="form-control" required onchange="loadJadwal()">
                            <option value="">-- Pilih Aula --</option>
                            <?php while ($a = mysqli_fetch_assoc($aulas)): ?>
                            <option value="<?php echo $a['id']; ?>" 
                                    data-kapasitas="<?php echo $a['kapasitas']; ?>"
                                    data-fasilitas="<?php echo $a['fasilitas']; ?>">
                                <?php echo $a['nama_aula']; ?> (Kapasitas: <?php echo $a['kapasitas']; ?> orang)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_kegiatan">Nama Kegiatan *</label>
                        <input type="text" id="nama_kegiatan" name="nama_kegiatan" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal_mulai">Tanggal Mulai *</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" 
                               min="<?php echo date('Y-m-d'); ?>" required onchange="loadJadwal()">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal_selesai">Tanggal Selesai *</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control" 
                               min="<?php echo date('Y-m-d'); ?>" required onchange="loadJadwal()">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="waktu_mulai">Waktu Mulai *</label>
                        <input type="time" id="waktu_mulai" name="waktu_mulai" class="form-control" required>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="waktu_selesai">Waktu Selesai *</label>
                        <input type="time" id="waktu_selesai" name="waktu_selesai" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="jumlah_peserta">Jumlah Peserta (estimasi)</label>
                        <input type="number" id="jumlah_peserta" name="jumlah_peserta" class="form-control" min="1">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" class="form-control" rows="4" 
                          placeholder="Deskripsi kegiatan, keperluan, dll"></textarea>
            </div>

            <div id="jadwalInfo" class="alert alert-info" style="display: none;">
                <strong>⚠️ Jadwal Aula yang Sudah Terpakai:</strong>
                <div id="jadwalList"></div>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn btn-secondary">Reset</button>
                <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
            </div>
        </form>
    </div>
</div>

<script>
// Validasi tanggal
document.getElementById('tanggal_mulai').addEventListener('change', function() {
    document.getElementById('tanggal_selesai').min = this.value;
    if (document.getElementById('tanggal_selesai').value < this.value) {
        document.getElementById('tanggal_selesai').value = this.value;
    }
});

// Load jadwal yang sudah terpakai
function loadJadwal() {
    const aulaId = document.getElementById('aula_id').value;
    const tanggalMulai = document.getElementById('tanggal_mulai').value;
    const tanggalSelesai = document.getElementById('tanggal_selesai').value;
    
    if (aulaId && tanggalMulai && tanggalSelesai) {
        fetch(`../proses/cek_jadwal.php?aula_id=${aulaId}&tanggal_mulai=${tanggalMulai}&tanggal_selesai=${tanggalSelesai}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let html = '<ul>';
                    data.forEach(item => {
                        html += `<li>${item.nama_kegiatan} - ${item.tanggal_mulai} s/d ${item.tanggal_selesai} (${item.waktu_mulai} - ${item.waktu_selesai})</li>`;
                    });
                    html += '</ul>';
                    
                    document.getElementById('jadwalList').innerHTML = html;
                    document.getElementById('jadwalInfo').style.display = 'block';
                } else {
                    document.getElementById('jadwalInfo').style.display = 'none';
                }
            });
    }
}

// Validasi form sebelum submit
document.getElementById('pengajuanForm').addEventListener('submit', function(e) {
    const waktuMulai = document.getElementById('waktu_mulai').value;
    const waktuSelesai = document.getElementById('waktu_selesai').value;
    
    if (waktuSelesai <= waktuMulai) {
        e.preventDefault();
        alert('Waktu selesai harus lebih besar dari waktu mulai');
        return false;
    }
});
</script>

<?php include '../includes/footer.php'; ?>