# 🔐 Panduan Reset Password - Sistem Aula

Karena Anda lupa password dan sudah mengubah default password, berikut beberapa cara untuk masuk kembali:

## ✅ **CARA 1: Menggunakan Script Reset Password (PALING MUDAH)**

### Langkah-Langkah:

1. **Buka browser** dan akses:
   ```
   http://localhost/aula-kemenag-jombang/reset_password.php
   ```

2. **Di form yang muncul, isi:**
   - **Username:** masukkan username Anda (lihat daftar di bawah form)
   - **Password Baru:** password yang ingin Anda gunakan (minimal 6 karakter)
   - **Konfirmasi Password:** ketik ulang password yang sama

3. **Klik tombol "Reset Password"**

4. **Jika berhasil**, akan muncul pesan sukses. Silakan login dengan username dan password baru di:
   ```
   http://localhost/aula-kemenag-jombang/auth/login.php
   ```

### Daftar User yang Tersedia:
- **admin** - Administrator
- **pimpinan** - Pimpinan Kemenag
- **muhammadridwan** - Muhammad Ridwan Hanif (User - Keuangan)
- **syauqimurtadlo** - Muhammad Syauqi Murtadlo (User - Pendma)
- **linamarlina** - Lina Marlina (User - PD Pontren)

---

## 📊 **CARA 2: Menggunakan Database Directly (UNTUK ADVANCED USERS)**

Jika Anda ingin langsung mengubah password di database:

### Via phpMyAdmin:

1. **Buka phpMyAdmin:**
   ```
   http://localhost/phpmyadmin
   ```

2. **Login dengan:**
   - Username: `root`
   - Password: (kosong atau sesuai konfigurasi Anda)

3. **Pilih database** `aula_kemenag` → tabel `users`

4. **Klik "Edit" pada user yang ingin direset passwordnya**

5. **Di field password**, ubah ke salah satu hash password ini:
   - **Untuk password: "admin123"**
     ```
     $2y$12$/E8B.xbXJhPFZqV5IrJ.eu4Ey4GFDtK.OFr.PndwUYkRGayIYAvVW
     ```
   
   - **Untuk password: "password"**
     ```
     $2y$12$G2dwbU/wfwQmVLbPTrqAXOCFb1vJrUqUvE0qLvJJwULz1hd0cKfcW
     ```

6. **Klik "Go"** untuk menyimpan

7. **Login dengan password baru Anda**

---

## 🖥️ **CARA 3: Via Command Line (MySQL Command)**

Jika Anda nyaman dengan command line:

```bash
# 1. Masuk ke MySQL
mysql -u root -p

# 2. Pilih database
USE aula_kemenag;

# 3. Update password (contoh: ubah admin password)
UPDATE users SET password = '$2y$12$/E8B.xbXJhPFZqV5IrJ.eu4Ey4GFDtK.OFr.PndwUYkRGayIYAvVW' WHERE username = 'admin';

# 4. Exit
exit
```

---

## ⚠️ **CATATAN PENTING:**

1. **Script reset_password.php sudah dihapus otomatis** setelah Anda selesai untuk alasan keamanan
2. Jika Anda ingin menghapusnya manual, cukup delete file `reset_password.php`
3. Pastikan tidak ada orang lain yang memiliki akses ke komputer saat reset
4. Setelah berhasil login, ubah password Anda ke yang lebih kuat

---

## 🚀 **QUICK START:**

**Langkah paling cepat (5 menit):**
1. Buka: `http://localhost/aula-kemenag-jombang/reset_password.php`
2. Isi form: username + password baru
3. Klik Reset
4. Login dengan password baru
5. Masuk ke dashboard ✅

---

## 🎯 **Terkait Pertanyaan Anda:**

> "Password default di README.md sudah pernah saya ganti, bagaimana caranya masuk lagi?"

**Solusi:** Gunakan **Cara 1** di atas (script reset password). Ini adalah cara tercepat dan teraman tanpa perlu mengakses database secara manual.

Jika ada pertanyaan, silakan tanyakan lebih lanjut!
