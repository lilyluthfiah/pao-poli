-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 04, 2026 at 08:15 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pengumumanakademik_php`
--

-- --------------------------------------------------------

--
-- Table structure for table `beasiswa`
--

DROP TABLE IF EXISTS `beasiswa`;
CREATE TABLE IF NOT EXISTS `beasiswa` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci NOT NULL,
  `file_beasiswa` text COLLATE utf8mb4_general_ci,
  `syarat` text COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `tanggal_akhir` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beasiswa`
--

INSERT INTO `beasiswa` (`id`, `nama`, `deskripsi`, `file_beasiswa`, `syarat`, `tanggal`, `tanggal_akhir`) VALUES
(1, 'Bidik Misi', '<p>adasdsasadasdadasd</p>', NULL, 'ijazah, skhu', '2026-01-03', '2026-02-03');

-- --------------------------------------------------------

--
-- Table structure for table `beasiswa_syarat`
--

DROP TABLE IF EXISTS `beasiswa_syarat`;
CREATE TABLE IF NOT EXISTS `beasiswa_syarat` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `beasiswa_id` bigint NOT NULL,
  `nama_syarat` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tahap` enum('Pendaftaran','Lulus') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pendaftaran',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwalkuliah`
--

DROP TABLE IF EXISTS `jadwalkuliah`;
CREATE TABLE IF NOT EXISTS `jadwalkuliah` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `dosen` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `matakuliah` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ruang` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `waktu` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwalkuliah`
--

INSERT INTO `jadwalkuliah` (`id`, `user_id`, `dosen`, `matakuliah`, `ruang`, `waktu`) VALUES
(2, 1, 'Fahrul Adib', 'Struktur Data', 'F-12', '10:00-12:00');

-- --------------------------------------------------------

--
-- Table structure for table `jadwalujian`
--

DROP TABLE IF EXISTS `jadwalujian`;
CREATE TABLE IF NOT EXISTS `jadwalujian` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `dosen` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `matakuliah` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `ruang` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `waktu` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwalujian`
--

INSERT INTO `jadwalujian` (`id`, `user_id`, `dosen`, `matakuliah`, `tanggal`, `ruang`, `waktu`) VALUES
(4, 1, 'Inumaki Toge', 'Pemrograman', '2026-01-06', 'A-12', '08:00 - 10:00');

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

DROP TABLE IF EXISTS `notifikasi`;
CREATE TABLE IF NOT EXISTS `notifikasi` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `pesan` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Belum Dibaca',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id`, `user_id`, `judul`, `pesan`, `status`, `created_at`) VALUES
(10, 1, 'Pendaftaran Beasiswa Baru', 'Mahasiswa Gojo Satoru (22040095) telah mendaftar beasiswa dan menunggu konfirmasi.', 'Belum Dibaca', '2026-01-04 14:06:07'),
(11, 2, 'Status Pendaftaran Beasiswa', 'Status pendaftaran Anda untuk beasiswa \"Bidik Misi\" telah diperbarui menjadi: Menunggu Konfirmasi.', 'Belum Dibaca', '2026-01-04 14:08:23'),
(12, 2, 'Status Pendaftaran Beasiswa', 'Status pendaftaran Anda untuk beasiswa \"Bidik Misi\" telah diperbarui menjadi: Diterima.', 'Belum Dibaca', '2026-01-04 14:08:28'),
(13, 2, 'Jadwal Ujian Baru', 'Jadwal ujian mata kuliah Pemrograman telah ditambahkan.', 'Belum Dibaca', '2026-01-04 14:19:41'),
(14, 4, 'Jadwal Ujian Baru', 'Jadwal ujian mata kuliah Pemrograman telah ditambahkan.', 'Belum Dibaca', '2026-01-04 14:19:41'),
(15, 1, 'Pendaftaran Beasiswa Baru', 'Mahasiswa Gojo Satoru (23214124124) telah mendaftar beasiswa dan menunggu konfirmasi.', 'Belum Dibaca', '2026-01-04 14:25:05'),
(16, 5, 'Status Pendaftaran Beasiswa', 'Status pendaftaran Anda untuk beasiswa \"Bidik Misi\" telah diperbarui menjadi: Diterima.', 'Belum Dibaca', '2026-01-04 14:25:33');

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran`
--

DROP TABLE IF EXISTS `pendaftaran`;
CREATE TABLE IF NOT EXISTS `pendaftaran` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `beasiswa_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `namalengkap` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nim` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `prodi` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `no_rekening` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `file_lengkapi` text COLLATE utf8mb4_general_ci,
  `tanggal` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `beasiswa_id` (`beasiswa_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pendaftaran`
--

INSERT INTO `pendaftaran` (`id`, `beasiswa_id`, `user_id`, `namalengkap`, `nim`, `prodi`, `no_rekening`, `file`, `file_lengkapi`, `tanggal`, `status`) VALUES
(4, 1, 2, 'Sudendev', '182083746657', 'Sistem Informasi', '1234567890', '1767432694_format.pdf', NULL, '2026-01-03', 'Diterima'),
(5, 1, 2, 'Gojo Satoru', '22040095', 'Teknik Informatika', 'DANA: 082761524512', '1767510367_DEF_images (2).jfif', '1767510541_LENGKAPI_monitoring_iot.zip', '2026-01-04', 'Diterima');

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran_files`
--

DROP TABLE IF EXISTS `pendaftaran_files`;
CREATE TABLE IF NOT EXISTS `pendaftaran_files` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `pendaftaran_id` bigint NOT NULL,
  `syarat_id` bigint NOT NULL,
  `file` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `pendaftaran_id` (`pendaftaran_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

DROP TABLE IF EXISTS `pengumuman`;
CREATE TABLE IF NOT EXISTS `pengumuman` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `judul`, `deskripsi`, `tanggal`) VALUES
(2, 'Modul Sosialisasi Keselamatan Berlalulintas', '<p>asdasdsadasdsadadad asdsa asda</p><p>asdsadsa</p><p>sadsadas</p><p>saddad</p>', '2026-01-03'),
(4, 'Pengumuman 2', '<p>ini pengumuman ke 2</p>', '2026-01-03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nimataunidn` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nohp` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_general_ci NOT NULL,
  `prodi` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `kelas` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `nimataunidn`, `nohp`, `alamat`, `prodi`, `kelas`, `role`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$2qpptU3QNOwNHSW9j.4tBu7GpjCeASX/yL5vPvSpv54m8l4jytrO6', '1231312421321', '082282076702', 'Palembang', 'Sistem Informasi', NULL, 'Admin'),
(2, 'Sudendev', 'sudendev@gmail.com', '$2y$10$JSGebb8vrj3Rr49gYRfQ9eWGqKiIijGg0k.an88zciHQiF9EDPXxW', '14453242342123', '082282076702', 'Palembang', 'Sistem Informasi', 'F-11', 'Mahasiswa'),
(5, 'Gojo Satoru', 'gojosatoru@gmail.com', '$2y$10$E5unBjpBkSclp7q0bVKnTe.PFidrlPcyf05.VgHXhNloRnD9aKmwK', '214214124124', '089613325456', 'jln.besitang Gang Damai, Lr.Teratai', 'Teknik Informatika', 'A-12', 'Mahasiswa');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `pendaftaran_ibfk_1` FOREIGN KEY (`beasiswa_id`) REFERENCES `beasiswa` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `pendaftaran_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `pendaftaran_files`
--
ALTER TABLE `pendaftaran_files`
  ADD CONSTRAINT `pendaftaran_files_ibfk_1` FOREIGN KEY (`pendaftaran_id`) REFERENCES `pendaftaran` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
