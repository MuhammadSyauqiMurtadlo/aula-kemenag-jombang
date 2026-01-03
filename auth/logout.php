<?php
session_start();
require_once '../config/database.php';

// Hapus semua session
session_unset();
session_destroy();

// Set flash message
session_start();
set_flash_message('success', 'Anda berhasil logout');

// Redirect ke login
redirect('login.php');
?>