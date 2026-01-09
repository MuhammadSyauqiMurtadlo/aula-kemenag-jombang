# Sistem Peminjaman Aula Kemenag Jombang

Aplikasi web untuk mengelola peminjaman/penggunaan aula di Kementerian Agama Kabupaten Jombang. Dibangun dengan **PHP Native** (tanpa framework) + MySQL.

## 🎯 Fitur Utama

### Admin

- ✅ Manajemen data aula
- ✅ Manajemen data seksi
- ✅ Manajemen user
- ✅ Approve/reject pengajuan aula
- ✅ Laporan penggunaan aula
- ✅ Histori lengkap

### Pimpinan

- ✅ Dashboard statistik
- ✅ Laporan penggunaan
- ✅ Grafik tren pengajuan
- ✅ View-only (read-only)

### User

- ✅ Ajukan peminjaman aula
- ✅ Cek status pengajuan
- ✅ Batalkan pengajuan (jika masih menunggu)
- ✅ Histori seksi sendiri

## 🛠️ Teknologi

- PHP 7.4+ (Native, tanpa framework)
- MySQL 5.7+
- HTML5 + CSS3
- JavaScript (Vanilla)
- Session-based Authentication

## 📦 Instalasi

### 1. Persiapan Server

Pastikan sudah install:

- Apache/Nginx
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- phpMyAdmin (opsional)

**Untuk XAMPP/WAMPP:**

- Download dan install XAMPP
- Start Apache dan MySQL

### 2. Download dan Extract

```bash
# Download aplikasi (atau clone dari repo)
# Extract ke folder htdocs (XAMPP) atau www (WAMPP)
```

### 3. Setup Database

**Cara 1: Manual**

1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Buat database baru bernama `aula_kemenag`
3. Import file `database/aula_kemenag.sql`

**Cara 2: Command Line**

```bash
mysql -u root -p
CREATE DATABASE aula_kemenag;
USE aula_kemenag;
SOURCE /path/to/database/aula_kemenag.sql;
```

### 4. Konfigurasi Database

Edit file `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Sesuaikan dengan username MySQL Anda
define('DB_PASS', '');              // Sesuaikan dengan password MySQL Anda
define('DB_NAME', 'aula_kemenag');
```

### 5. Akses Aplikasi

Buka browser dan akses: `http://localhost/aula-kemenag`

## 🔐 Login Default

### Admin

- **Username:** `admin`
- **Password:** `admin123`

### Pimpinan

- **Username:** `pimpinan`
- **Password:** `pimpinan123`

### User

- **Username:** `user_keuangan`
- **Password:** `user123`

⚠️ **PENTING:** Ubah password default setelah login pertama!

## 📁 Struktur Folder

```
aula-kemenag/
├── config/
│   └── database.php          # Konfigurasi database
├── auth/
│   ├── login.php             # Halaman login
│   ├── proses_login.php      # Proses autentikasi
│   └── logout.php            # Proses logout
├── admin/                    # Modul admin
│   ├── dashboard.php
│   ├── aula.php
│   ├── seksi.php
│   ├── users.php
│   ├── persetujuan.php
│   ├── laporan.php
│   └── histori.php
├── pimpinan/                 # Modul pimpinan
│   ├── dashboard.php
│   ├── laporan.php
│   └── statistik.php
├── user/                     # Modul user
│   ├── dashboard.php
│   ├── pengajuan.php
│   ├── status.php
│   └── histori.php
├── proses/                   # Backend logic
│   ├── aula.php
│   ├── seksi.php
│   ├── user.php
│   ├── pengajuan.php
│   ├── persetujuan.php
│   └── cek_jadwal.php
├── includes/                 # Template components
│   ├── header.php
│   └── footer.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── database/
│   └── aula_kemenag.sql      # SQL dump
└── index.php                 # Landing page
```

## 🔒 Keamanan

Aplikasi ini sudah dilengkapi dengan:

- ✅ Prepared Statements (mencegah SQL Injection)
- ✅ Password Hashing (bcrypt)
- ✅ Session-based Authentication
- ✅ Input Sanitization
- ✅ RBAC (Role-Based Access Control)
- ✅ XSS Protection

## 🎨 Kustomisasi

### Menambah Aula Baru

1. Login sebagai Admin
2. Menu "Data Aula" > Tambah Aula
3. Isi form dan simpan

### Menambah Seksi Baru

1. Login sebagai Admin
2. Menu "Data Seksi" > Tambah Seksi
3. Isi form dan simpan

### Menambah User Baru

1. Login sebagai Admin
2. Menu "Data User" > Tambah User
3. Pilih role dan seksi (jika user biasa)
4. Simpan

## 📊 Status Pengajuan

1. **Menunggu Persetujuan** - Pengajuan baru, belum diproses admin
2. **Disetujui** - Admin menyetujui, aula bisa digunakan
3. **Ditolak** - Admin menolak dengan alasan
4. **Dibatalkan** - User membatalkan sendiri
5. **Selesai** - Kegiatan sudah berlangsung

## 🐛 Troubleshooting

### Database Connection Error

```
Koneksi database gagal: Access denied for user...
```

**Solusi:** Periksa username dan password di `config/database.php`

### 404 Not Found

```
The requested URL was not found on this server
```

**Solusi:** Pastikan folder aplikasi ada di `htdocs` dan akses dengan URL yang benar

### Session Not Working

**Solusi:** Pastikan `session.save_path` di `php.ini` memiliki permission yang benar

### Bentrok Jadwal Tidak Terdeteksi

**Solusi:** Refresh halaman atau clear cache browser

## 📝 Catatan Penting

- Password default HARUS diubah setelah instalasi
- Backup database secara berkala
- Gunakan HTTPS jika production
- Set permission folder sesuai kebutuhan (755 untuk folder, 644 untuk file)
- Nonaktifkan error reporting di production

## 🤝 Support

Untuk pertanyaan atau bantuan:

- Email: support@example.com
- WA: 0812-xxxx-xxxx

## 📄 Lisensi

© 2024 Kementerian Agama Kabupaten Jombang. All rights reserved.

---

**Dibuat dengan ❤️ oleh Muhammad Syauqi Murtadlo menggunakan PHP Native**
