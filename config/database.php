<?php
/**
 * Database Configuration
 * Configures the database connection and common functions
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'aula_kemenag');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Koneksi database gagal:" . mysqli_connect_error());
}

// Set character set to utf8
mysqli_set_charset($conn, "utf8");

// Timezone setting
date_default_timezone_set('Asia/Jakarta');

/**
 * Function to clean input (prevent XSS)
 */
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

// function to redirect
function redirect($url) {
    header("Location: " . $url);
    exit();
} 

// function to set flash message
function set_flash_message($type, $message) {
    $_SESSION['flash_type'] = $type; // success, danger, warning, info
    $_SESSION['flash_message'] = $message;
}

// function to get and delete flash message
function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'];
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return ['type' => $type, 'message' => $message];
    }
    return null;
}

// function to check login
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role_id']);
}

// function to check role
function check_role($allowed_roles) {
    if (!is_logged_in()) {
        redirect('../auth/login.php');
    }
// !Catatan penting : set_flash dibawah ini saya ikut aturan PHP (kalau error berarti disesuiakan saja dengan contoh yang ada di claude AI, tidak usah pakai '_message')
    if (!empty($allowed_roles) && !in_array($_SESSION['role_id'], $allowed_roles)) {
        set_flash_message('danger', 'Anda tidak memiliki akses ke halaman ini');
        redirect('../auth/login.php');
    }
}

// Function to format date Indonesia
function format_tanggal($date) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November',
        'Desember'
    );

    $split = explode('-', $date);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Function to generate code submission
function generate_kode_pengajuan() {
    $prefix = 'AUL';
    $date = date('Ymd');
    $random = rand(1000, 9999);
    return $prefix . $date . $random;
}

// Function to check schedule conflict
function cek_bentrok_jadwal($aula_id, $tanggal_mulai, $tanggal_selesai, $waktu_mulai, $waktu_selesai, $pengajuan_id = null) {
    global $conn;
    
    $query = "SELECT * FROM pengajuan_aula 
              WHERE aula_id = ? 
              AND status_id IN (1, 2) 
              AND (
                  (tanggal_mulai <= ? AND tanggal_selesai >= ?)
                  OR (tanggal_mulai <= ? AND tanggal_selesai >= ?)
                  OR (tanggal_mulai >= ? AND tanggal_selesai <= ?)
              )
              AND (
                  (waktu_mulai < ? AND waktu_selesai > ?)
                  OR (waktu_mulai < ? AND waktu_selesai > ?)
                  OR (waktu_mulai >= ? AND waktu_selesai <= ?)
              )";
    
    if ($pengajuan_id) {
        $query .= " AND id != ?";
    }
    
    $stmt = mysqli_prepare($conn, $query);
    
    if ($pengajuan_id) {
        // PERHATIKAN: tambah 1 huruf 's' jadi "issssssssssss" (13 huruf 's')
        mysqli_stmt_bind_param($stmt, "issssssssssssi", 
            $aula_id, 
            $tanggal_mulai, $tanggal_mulai,
            $tanggal_selesai, $tanggal_selesai,
            $tanggal_mulai, $tanggal_selesai,
            $waktu_selesai, $waktu_mulai,
            $waktu_selesai, $waktu_mulai,
            $waktu_mulai, $waktu_selesai,
            $pengajuan_id
        );
    } else {
        // PERHATIKAN: tambah 1 huruf 's' jadi "issssssssssss" (13 huruf 's')
        mysqli_stmt_bind_param($stmt, "issssssssssss", 
            $aula_id, 
            $tanggal_mulai, $tanggal_mulai,
            $tanggal_selesai, $tanggal_selesai,
            $tanggal_mulai, $tanggal_selesai,
            $waktu_selesai, $waktu_mulai,
            $waktu_selesai, $waktu_mulai,
            $waktu_mulai, $waktu_selesai
        );
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_num_rows($result) > 0;
}
?>
