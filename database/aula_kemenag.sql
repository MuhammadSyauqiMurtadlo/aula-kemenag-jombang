-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 12, 2026 at 02:14 AM
-- Server version: 8.0.30
-- PHP Version: 8.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aula_kemenag`
--

-- --------------------------------------------------------

--
-- Table structure for table `aula`
--

CREATE TABLE `aula` (
  `id` int NOT NULL,
  `nama_aula` varchar(100) NOT NULL,
  `kapasitas` int DEFAULT NULL,
  `fasilitas` text,
  `keterangan` text,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `aula`
--

INSERT INTO `aula` (`id`, `nama_aula`, `kapasitas`, `fasilitas`, `keterangan`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Aula Darul Hikmah', 200, 'AC, Proyektor, Sound System', 'Aula utama lantai 1', 'aktif', '2025-12-26 03:11:13', '2025-12-26 03:11:13'),
(2, 'Aula Al Muhajir', 150, 'AC, Proyektor', 'Aula lantai 2', 'aktif', '2025-12-26 03:11:13', '2025-12-26 03:11:13'),
(3, 'Aula Atas', 100, 'Kipas Angin, Sound System', 'Aula lantai 3', 'aktif', '2025-12-26 03:11:13', '2025-12-26 03:11:13'),
(4, 'Aula PLHUT', 80, 'AC, Proyektor', 'Aula khusus PLHUT', 'aktif', '2025-12-26 03:11:13', '2025-12-26 03:11:13'),
(5, 'Aula Lainnya', 50, 'Standar', 'Aula cadangan', 'aktif', '2025-12-26 03:11:13', '2025-12-26 03:11:13');

-- --------------------------------------------------------

--
-- Table structure for table `histori_penggunaan`
--

CREATE TABLE `histori_penggunaan` (
  `id` int NOT NULL,
  `pengajuan_id` int NOT NULL,
  `aula_id` int NOT NULL,
  `seksi_id` int NOT NULL,
  `nama_kegiatan` varchar(200) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time NOT NULL,
  `jumlah_peserta` int DEFAULT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `histori_penggunaan`
--

INSERT INTO `histori_penggunaan` (`id`, `pengajuan_id`, `aula_id`, `seksi_id`, `nama_kegiatan`, `tanggal_mulai`, `tanggal_selesai`, `waktu_mulai`, `waktu_selesai`, `jumlah_peserta`, `keterangan`, `created_at`) VALUES
(1, 4, 1, 6, 'Pembinaan ASN', '2026-01-25', '2026-01-25', '07:00:00', '10:00:00', 200, 'Pembinaan ASN oleh Kepala kanwil Provinsi Jawa Timur', '2026-01-08 01:05:15');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_aula`
--

CREATE TABLE `pengajuan_aula` (
  `id` int NOT NULL,
  `kode_pengajuan` varchar(50) NOT NULL,
  `user_id` int NOT NULL,
  `seksi_id` int NOT NULL,
  `aula_id` int NOT NULL,
  `nama_pemohon` varchar(100) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `waktu_mulai` time NOT NULL,
  `waktu_selesai` time NOT NULL,
  `nama_kegiatan` varchar(200) NOT NULL,
  `jumlah_peserta` int DEFAULT NULL,
  `keterangan` text,
  `status_id` int DEFAULT '1',
  `alasan_penolakan` text,
  `disetujui_oleh` int DEFAULT NULL,
  `tanggal_persetujuan` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengajuan_aula`
--

INSERT INTO `pengajuan_aula` (`id`, `kode_pengajuan`, `user_id`, `seksi_id`, `aula_id`, `nama_pemohon`, `tanggal_mulai`, `tanggal_selesai`, `waktu_mulai`, `waktu_selesai`, `nama_kegiatan`, `jumlah_peserta`, `keterangan`, `status_id`, `alasan_penolakan`, `disetujui_oleh`, `tanggal_persetujuan`, `created_at`, `updated_at`) VALUES
(1, 'AUL202601086859', 3, 1, 3, 'User Keuangan', '2026-01-09', '2026-01-09', '08:00:00', '11:00:00', 'Rapat Koordinasi Internal', 50, 'Rapat evaluasi program kerja bulanan', 1, NULL, NULL, NULL, '2026-01-07 22:11:30', '2026-01-07 22:11:30'),
(2, 'AUL202601088178', 4, 7, 2, 'Muhammad Syauqi Murtadlo', '2026-01-13', '2026-01-15', '08:00:00', '12:00:00', 'Workshop Digitalisasi Arsip', 100, 'Workshop dua hari pengelolaan arsip digital', 2, NULL, 1, '2026-01-08 06:09:40', '2026-01-07 23:03:52', '2026-01-07 23:09:40'),
(3, 'AUL202601088113', 4, 7, 4, 'Muhammad Syauqi Murtadlo', '2026-01-11', '2026-01-11', '10:00:00', '11:00:00', 'Rapat Persiapan HAB', 40, 'Peringatan Hari Amal Bhakti', 1, NULL, NULL, NULL, '2026-01-07 23:08:44', '2026-01-07 23:08:44'),
(4, 'AUL202601088077', 5, 6, 1, 'Lina Marlina', '2026-01-25', '2026-01-25', '07:00:00', '10:00:00', 'Pembinaan ASN', 200, 'Pembinaan ASN oleh Kepala kanwil Provinsi Jawa Timur', 2, NULL, 1, '2026-01-08 08:05:15', '2026-01-08 01:02:05', '2026-01-08 01:05:15');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `nama_role` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nama_role`, `created_at`) VALUES
(1, 'Admin', '2025-12-26 03:08:15'),
(2, 'Pimpinan', '2025-12-26 03:08:15'),
(3, 'User', '2025-12-26 03:08:15');

-- --------------------------------------------------------

--
-- Table structure for table `seksi`
--

CREATE TABLE `seksi` (
  `id` int NOT NULL,
  `nama_seksi` varchar(100) NOT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `seksi`
--

INSERT INTO `seksi` (`id`, `nama_seksi`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Keuangan', 'Bagian Keuangan', '2025-12-26 03:09:40', '2025-12-26 03:09:40'),
(2, 'PHU', 'Penyelenggara Haji dan Umroh', '2025-12-26 03:09:40', '2025-12-26 03:09:40'),
(3, 'Perencanaan', 'Bagian Perencanaan', '2025-12-26 03:09:40', '2025-12-26 03:09:40'),
(4, 'Umum', 'Bagian Umum', '2025-12-26 03:09:40', '2025-12-26 03:09:40'),
(5, 'Bimas Islam', 'Bimbingan Masyarakat Islam', '2025-12-26 03:09:40', '2025-12-26 03:09:40'),
(6, 'PD Pontren', 'Pendidikan Diniyah dan Pondok Pesantren', '2025-12-26 03:09:40', '2025-12-26 03:09:40'),
(7, 'Pendma', 'Pendidikan Madrasah', '2025-12-26 03:09:40', '2025-12-26 03:09:40'),
(8, 'Penzawa', 'Penyelenggara Zakat dan Wakaf', '2025-12-26 03:09:40', '2025-12-26 03:09:40'),
(9, 'Kepegawaian', 'Bagian Kepegawaian', '2025-12-26 03:09:40', '2025-12-26 03:09:40'),
(10, 'Lainnya', 'Seksi Lainnya', '2025-12-26 03:09:40', '2025-12-26 03:09:40');

-- --------------------------------------------------------

--
-- Table structure for table `status_pengajuan`
--

CREATE TABLE `status_pengajuan` (
  `id` int NOT NULL,
  `nama_status` varchar(50) NOT NULL,
  `warna` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `status_pengajuan`
--

INSERT INTO `status_pengajuan` (`id`, `nama_status`, `warna`, `created_at`) VALUES
(1, 'Menunggu Persetujuan', 'warning', '2025-12-26 03:12:23'),
(2, 'Disetujui', 'success', '2025-12-26 03:12:23'),
(3, 'Ditolak', 'danger', '2025-12-26 03:12:23'),
(4, 'Dibatalkan', 'secondary', '2025-12-26 03:12:23'),
(5, 'Selesai', 'info', '2025-12-26 03:12:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `role_id` int NOT NULL,
  `seksi_id` int DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `no_hp`, `role_id`, `seksi_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$12$/E8B.xbXJhPFZqV5IrJ.eu4Ey4GFDtK.OFr.PndwUYkRGayIYAvVW', 'Administrator', 'admin@kemenag.go.id', '', 1, NULL, 'aktif', '2025-12-26 03:13:33', '2026-01-07 22:34:55'),
(2, 'pimpinan', '$2y$12$eyBmw0BTBfEK86yVleAUbu.Y3EuNPI.PD0eqT6TbhgnKHdbkVjRPa', 'Pimpinan Kemenag', 'pimpinan@kemenag.go.id', '', 2, NULL, 'aktif', '2025-12-26 03:13:33', '2026-01-07 22:50:27'),
(3, 'muhammadridwan', '$2y$12$HlbsMNcqM7.ASLUggpzNoOg0Xk16HwGgjJQMZp6zMfRGRG8yDe4Qy', 'Muhammad Ridwan Hanif', 'keuangan@kemenag.go.id', '', 3, 1, 'aktif', '2025-12-26 03:13:33', '2026-01-08 00:51:47'),
(4, 'syauqimurtadlo', '$2y$12$hk/bte2kb3mtZq9rFerfBeL870sL95ec26Y8lEnU2Q8bzkDXKhFYa', 'Muhammad Syauqi Murtadlo', 'sauki084@gmail.com', '081233970793', 3, 7, 'aktif', '2026-01-07 22:39:50', '2026-01-07 22:39:50'),
(5, 'linamarlina', '$2y$12$JnoMSg/T2UbVNyCaSLGLFO/j5vvNW5rG1JMYqLRsltq2JRBk1lUqG', 'Lina Marlina', 'lina@gmail.com', '089976575775', 3, 6, 'aktif', '2026-01-08 00:58:46', '2026-01-08 00:58:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aula`
--
ALTER TABLE `aula`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `histori_penggunaan`
--
ALTER TABLE `histori_penggunaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_id` (`pengajuan_id`),
  ADD KEY `aula_id` (`aula_id`),
  ADD KEY `seksi_id` (`seksi_id`),
  ADD KEY `idx_histori_tanggal` (`tanggal_mulai`,`tanggal_selesai`);

--
-- Indexes for table `pengajuan_aula`
--
ALTER TABLE `pengajuan_aula`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pengajuan` (`kode_pengajuan`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `seksi_id` (`seksi_id`),
  ADD KEY `disetujui_oleh` (`disetujui_oleh`),
  ADD KEY `idx_pengajuan_tanggal` (`tanggal_mulai`,`tanggal_selesai`),
  ADD KEY `idx_pengajuan_aula` (`aula_id`),
  ADD KEY `idx_pengajuan_status` (`status_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seksi`
--
ALTER TABLE `seksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status_pengajuan`
--
ALTER TABLE `status_pengajuan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `seksi_id` (`seksi_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aula`
--
ALTER TABLE `aula`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `histori_penggunaan`
--
ALTER TABLE `histori_penggunaan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pengajuan_aula`
--
ALTER TABLE `pengajuan_aula`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `seksi`
--
ALTER TABLE `seksi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `status_pengajuan`
--
ALTER TABLE `status_pengajuan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `histori_penggunaan`
--
ALTER TABLE `histori_penggunaan`
  ADD CONSTRAINT `histori_penggunaan_ibfk_1` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_aula` (`id`),
  ADD CONSTRAINT `histori_penggunaan_ibfk_2` FOREIGN KEY (`aula_id`) REFERENCES `aula` (`id`),
  ADD CONSTRAINT `histori_penggunaan_ibfk_3` FOREIGN KEY (`seksi_id`) REFERENCES `seksi` (`id`);

--
-- Constraints for table `pengajuan_aula`
--
ALTER TABLE `pengajuan_aula`
  ADD CONSTRAINT `pengajuan_aula_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pengajuan_aula_ibfk_2` FOREIGN KEY (`seksi_id`) REFERENCES `seksi` (`id`),
  ADD CONSTRAINT `pengajuan_aula_ibfk_3` FOREIGN KEY (`aula_id`) REFERENCES `aula` (`id`),
  ADD CONSTRAINT `pengajuan_aula_ibfk_4` FOREIGN KEY (`status_id`) REFERENCES `status_pengajuan` (`id`),
  ADD CONSTRAINT `pengajuan_aula_ibfk_5` FOREIGN KEY (`disetujui_oleh`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`seksi_id`) REFERENCES `seksi` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
