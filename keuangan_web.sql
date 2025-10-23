-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 23, 2025 at 12:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `keuangan_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `target_keuangan`
--

CREATE TABLE `target_keuangan` (
  `id` int(11) NOT NULL,
  `nama_target` varchar(100) NOT NULL,
  `target_jumlah` decimal(15,2) NOT NULL,
  `terkumpul` decimal(15,2) DEFAULT 0.00,
  `tanggal_target` date NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `target_keuangan`
--

INSERT INTO `target_keuangan` (`id`, `nama_target`, `target_jumlah`, `terkumpul`, `tanggal_target`, `kategori`, `created_at`) VALUES
(1, 'Tabungan Liburan', 5000000.00, 1500000.00, '2024-12-31', 'Tabungan', '2025-10-23 09:58:13'),
(2, 'Beli Laptop Baru', 10000000.00, 3000000.00, '2024-06-30', 'Elektronik', '2025-10-23 09:58:13'),
(3, 'Dana Darurat', 3000000.00, 1000000.00, '2024-03-31', 'Tabungan', '2025-10-23 09:58:13');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `jenis` varchar(20) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `jenis`, `jumlah`, `kategori`, `deskripsi`, `tanggal`) VALUES
(1, 'Pemasukan', 5000000.00, 'Gaji', 'Gaji bulan Januari', '2024-01-05 08:00:00'),
(2, 'Pengeluaran', 150000.00, 'Makanan', 'Makan siang restoran', '2024-01-05 12:30:00'),
(3, 'Pengeluaran', 200000.00, 'Transportasi', 'Bensin motor', '2024-01-06 09:15:00'),
(4, 'Pemasukan', 1000000.00, 'Freelance', 'Project website', '2024-01-07 14:20:00'),
(5, 'Pengeluaran', 500000.00, 'Belanja', 'Beli baju', '2024-01-08 16:45:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `target_keuangan`
--
ALTER TABLE `target_keuangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `target_keuangan`
--
ALTER TABLE `target_keuangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
