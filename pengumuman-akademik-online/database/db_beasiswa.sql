-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 30, 2025 at 11:15 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_beasiswa`
--

-- --------------------------------------------------------

--
-- Table structure for table `beasiswa`
--

CREATE TABLE `beasiswa` (
  `id` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `penyelenggara` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_akhir` date NOT NULL,
  `tanggal_seleksi` date NOT NULL,
  `tanggal_pengumuman` date NOT NULL,
  `min_ipk` decimal(3,2) NOT NULL,
  `min_semester` int NOT NULL,
  `allowed_prodi` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beasiswa_berkas`
--

CREATE TABLE `beasiswa_berkas` (
  `id` int NOT NULL,
  `beasiswa_id` int NOT NULL,
  `nama_berkas` varchar(100) NOT NULL,
  `tipe_file` varchar(20) NOT NULL,
  `max_size_mb` int NOT NULL,
  `wajib ENUM` enum('wajib','opsional') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beasiswa_persyaratan`
--

CREATE TABLE `beasiswa_persyaratan` (
  `id` int NOT NULL,
  `beasiswa_id` int NOT NULL,
  `persyaratan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_kuliah`
--

CREATE TABLE `jadwal_kuliah` (
  `id` int NOT NULL,
  `tanggal` date NOT NULL,
  `mata_kuliah` varchar(100) NOT NULL,
  `ruang` varchar(50) NOT NULL,
  `waktu` varchar(50) NOT NULL,
  `dosen` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jadwal_kuliah`
--

INSERT INTO `jadwal_kuliah` (`id`, `tanggal`, `mata_kuliah`, `ruang`, `waktu`, `dosen`, `created_at`) VALUES
(1, '2025-12-30', 'Dasar Pemrograman ', 'GU-706', '13.40 - 17.00', 'nur ', '2025-12-26 00:01:46'),
(2, '2025-09-29', 'PTI', 'GU-702', '08.40 - 12.00', 'kevin', '2025-12-26 00:01:46'),
(5, '2025-12-31', 'Sistem Komputer', 'TA 10.4', '13.40 - 17.00', 'nizan', '2025-12-26 03:32:58');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int NOT NULL,
  `nim` varchar(20) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `prodi` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan`
--

CREATE TABLE `pengajuan` (
  `id` int NOT NULL,
  `mahasiswa_id` int NOT NULL,
  `beasiswa_id` int NOT NULL,
  `rekening` varchar(50) NOT NULL,
  `status` enum('Menunggu','Diterima','Ditolak') NOT NULL DEFAULT 'Menunggu',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_berkas`
--

CREATE TABLE `pengajuan_berkas` (
  `id` int NOT NULL,
  `pengajuan_id` int NOT NULL,
  `nama_berkas` varchar(150) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `is_active`, `created_at`) VALUES
(7, 'Zaskia Deya Ramadhani', '$2y$10$PLFiZPjW17HNS7tUgTNipO3l/Xst5UoLeXcwbpOfCUIkMSi/WcMSq', 'admin', 1, '2025-12-30 08:20:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beasiswa`
--
ALTER TABLE `beasiswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `beasiswa_berkas`
--
ALTER TABLE `beasiswa_berkas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `beasiswa_id` (`beasiswa_id`);

--
-- Indexes for table `beasiswa_persyaratan`
--
ALTER TABLE `beasiswa_persyaratan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `beasiswa_id` (`beasiswa_id`);

--
-- Indexes for table `jadwal_kuliah`
--
ALTER TABLE `jadwal_kuliah`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim_unique` (`nim`);

--
-- Indexes for table `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_ibfk_1` (`beasiswa_id`),
  ADD KEY `mahasiswa_id` (`mahasiswa_id`);

--
-- Indexes for table `pengajuan_berkas`
--
ALTER TABLE `pengajuan_berkas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_id` (`pengajuan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beasiswa`
--
ALTER TABLE `beasiswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `beasiswa_berkas`
--
ALTER TABLE `beasiswa_berkas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `beasiswa_persyaratan`
--
ALTER TABLE `beasiswa_persyaratan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jadwal_kuliah`
--
ALTER TABLE `jadwal_kuliah`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuan`
--
ALTER TABLE `pengajuan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuan_berkas`
--
ALTER TABLE `pengajuan_berkas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `beasiswa_berkas`
--
ALTER TABLE `beasiswa_berkas`
  ADD CONSTRAINT `beasiswa_berkas_ibfk_1` FOREIGN KEY (`beasiswa_id`) REFERENCES `beasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `beasiswa_persyaratan`
--
ALTER TABLE `beasiswa_persyaratan`
  ADD CONSTRAINT `beasiswa_persyaratan_ibfk_1` FOREIGN KEY (`beasiswa_id`) REFERENCES `beasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD CONSTRAINT `pengajuan_ibfk_1` FOREIGN KEY (`beasiswa_id`) REFERENCES `beasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pengajuan_ibfk_2` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengajuan_berkas`
--
ALTER TABLE `pengajuan_berkas`
  ADD CONSTRAINT `pengajuan_berkas_ibfk_1` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
