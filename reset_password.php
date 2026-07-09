<?php
/**
 * Script Reset Password
 * Gunakan untuk reset password jika lupa
 */

require_once 'config/database.php';

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    
    $error = '';
    $success = '';
    
    // Validasi
    if (empty($username)) {
        $error = 'Username harus diisi!';
    } elseif (empty($new_password)) {
        $error = 'Password baru harus diisi!';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        // Cek apakah user ada
        $query = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            $error = 'Username tidak ditemukan!';
        } else {
            $user = mysqli_fetch_assoc($result);
            
            // Hash password baru
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            
            // Update password di database
            $update_query = "UPDATE users SET password = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "si", $hashed_password, $user['id']);
            
            if (mysqli_stmt_execute($update_stmt)) {
                $success = "Password untuk user <strong>$username</strong> berhasil direset!<br>Silakan login dengan password baru.";
            } else {
                $error = 'Gagal mengupdate password: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($update_stmt);
        }
        mysqli_stmt_close($stmt);
    }
}

// List semua user untuk referensi
$users_query = "SELECT u.id, u.username, u.nama_lengkap, r.nama_role FROM users u 
                LEFT JOIN roles r ON u.role_id = r.id 
                WHERE u.status = 'aktif' 
                ORDER BY u.role_id, u.username";
$users_result = mysqli_query($conn, $users_query);
$available_users = mysqli_fetch_all($users_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sistem Aula</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            font-size: 28px;
        }
        
        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: #c3e6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .hint {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin-top: 30px;
        }
        
        .hint h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .hint p {
            color: #666;
            font-size: 13px;
            margin-bottom: 8px;
            line-height: 1.5;
        }
        
        .user-list {
            max-height: 200px;
            overflow-y: auto;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 3px;
            font-size: 12px;
        }
        
        .user-item {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        
        .user-item:last-child {
            border-bottom: none;
        }
        
        .role-badge {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            margin-left: 5px;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Reset Password</h1>
        <p class="subtitle">Sistem Peminjaman Aula Kemenag Jombang</p>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                       placeholder="Masukkan username Anda" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">Password Baru</label>
                <input type="password" id="new_password" name="new_password" 
                       placeholder="Masukkan password baru (min. 6 karakter)" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       placeholder="Ketik ulang password baru" required>
            </div>
            
            <button type="submit">Reset Password</button>
        </form>
        
        <div class="hint">
            <h3>📋 Daftar User Aktif:</h3>
            <div class="user-list">
                <?php foreach ($available_users as $user): ?>
                    <div class="user-item">
                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                        <span style="color: #999;">- <?php echo htmlspecialchars($user['nama_lengkap']); ?></span>
                        <span class="role-badge"><?php echo htmlspecialchars($user['nama_role']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="back-link">
            <a href="auth/login.php">← Kembali ke Login</a>
        </div>
    </div>
</body>
</html>
