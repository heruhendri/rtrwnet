-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Jul 2025 pada 18.01
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `billingrtrwnet`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `action` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `data_pelanggan`
--

CREATE TABLE `data_pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `alamat_pelanggan` text NOT NULL,
  `telepon_pelanggan` varchar(50) NOT NULL,
  `email_pelanggan` varchar(100) DEFAULT '',
  `id_paket` int(11) DEFAULT NULL,
  `odp_id` int(11) DEFAULT NULL,
  `pop_id` int(11) DEFAULT NULL,
  `status_aktif` enum('aktif','nonaktif','isolir') NOT NULL DEFAULT 'aktif',
  `tgl_daftar` date NOT NULL,
  `tgl_expired` date DEFAULT NULL,
  `last_paid_date` date DEFAULT NULL,
  `mikrotik_username` varchar(100) DEFAULT '',
  `mikrotik_password` varchar(100) DEFAULT '',
  `mikrotik_profile` varchar(100) DEFAULT '',
  `mikrotik_service` varchar(20) DEFAULT 'pppoe',
  `mikrotik_caller_id` varchar(50) DEFAULT '',
  `mikrotik_routes` text DEFAULT '',
  `static_ip` varchar(20) DEFAULT '',
  `ip_pool` varchar(50) DEFAULT '',
  `mikrotik_comment` text DEFAULT '',
  `mikrotik_disabled` enum('yes','no') DEFAULT 'no',
  `sync_mikrotik` enum('yes','no') DEFAULT 'no',
  `last_sync` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status_pppoe` enum('active','isolated') DEFAULT 'active',
  `tanggal_isolasi` datetime DEFAULT NULL,
  `odp_port_id` int(11) DEFAULT NULL COMMENT 'ID Port ODP yang digunakan pelanggan',
  `onu_id` varchar(20) DEFAULT '',
  `signal_rx` varchar(20) DEFAULT '',
  `signal_tx` varchar(20) DEFAULT '',
  `ftth_status` enum('active','inactive','maintenance') DEFAULT 'active' COMMENT 'Status FTTH',
  `installation_date` date DEFAULT NULL COMMENT 'Tanggal instalasi',
  `technician` varchar(100) DEFAULT '',
  `ftth_notes` text DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `data_pelanggan`
--

INSERT INTO `data_pelanggan` (`id_pelanggan`, `nama_pelanggan`, `alamat_pelanggan`, `telepon_pelanggan`, `email_pelanggan`, `id_paket`, `odp_id`, `pop_id`, `status_aktif`, `tgl_daftar`, `tgl_expired`, `last_paid_date`, `mikrotik_username`, `mikrotik_password`, `mikrotik_profile`, `mikrotik_service`, `mikrotik_caller_id`, `mikrotik_routes`, `static_ip`, `ip_pool`, `mikrotik_comment`, `mikrotik_disabled`, `sync_mikrotik`, `last_sync`, `created_at`, `updated_at`, `status_pppoe`, `tanggal_isolasi`, `odp_port_id`, `onu_id`, `signal_rx`, `signal_tx`, `ftth_status`, `installation_date`, `technician`, `ftth_notes`) VALUES
(1, 'SUKIMAN', 'Jl. Mawar No. 5, RT 01/RW 02', '081211122233', '', NULL, NULL, NULL, 'aktif', '2025-07-09', '2025-07-10', NULL, 'suk998', '4pnHg4Sz', '100mbs', 'pppoe', '', '', '', '', '', 'no', 'yes', '2025-07-09 01:57:03', '2025-07-09 01:57:03', '2025-07-09 01:57:03', 'active', NULL, NULL, '', '', '', 'active', NULL, '', ''),
(2, 'JARWOTI', 'SUKABUMI', '085217197800', '', 5, NULL, NULL, 'aktif', '2025-07-10', '2025-07-15', NULL, 'jar578', 'dgic0Frl', '10mbs', 'pppoe', '', '', '', '', '', 'no', 'yes', '2025-07-10 06:48:06', '2025-07-10 06:48:06', '2025-07-10 06:48:06', 'active', NULL, NULL, '', '', '', 'active', NULL, '', ''),
(3, 'JARWOTIR', 'Jl. Mawar No. 5, RT 01/RW 02', '085217197800', '', 5, 1, 5, 'aktif', '0000-00-00', '2025-07-10', NULL, 'jarwodir', 'jarwodir', '10mbs', 'pppoe', '', '', '', '', '', 'no', 'yes', '2025-07-10 16:02:28', '2025-07-10 16:02:28', '2025-07-10 16:02:28', 'active', NULL, NULL, '', '', '', 'active', NULL, '', ''),
(4, 'IBU MAEY', 'Jl. Mawar No. 5, RT 01/RW 02', '08512445412', '', 5, NULL, NULL, 'aktif', '2025-07-11', '2025-08-11', NULL, 'ibu463', 'AbnbSXpf', '', 'pppoe', '', '', '', '', '', 'no', 'no', NULL, '2025-07-11 15:37:18', '2025-07-11 15:37:18', 'active', NULL, NULL, '', '', '', 'active', NULL, '', ''),
(5, 'JARWOTIR2', 'Jl. Mawar No. 5, RT 01/RW 02', '085217197800', '', 5, 1, 5, 'aktif', '0000-00-00', '2025-07-16', NULL, 'jarwodir1', 'jarwodir', '10mbs', 'pppoe', '', '', '', '', '', 'no', 'yes', '2025-07-11 15:40:13', '2025-07-11 15:40:13', '2025-07-11 15:40:13', 'active', NULL, NULL, '', '', '', 'active', NULL, '', ''),
(6, 'testing', 'Jl. Mawar No. 5, RT 01/RW 02', '081211122233', '', 5, 1, 5, 'aktif', '0000-00-00', '2025-07-25', NULL, 'testing', 'testing', '10mbs', 'pppoe', '', '', '', '', '', 'no', 'yes', '2025-07-11 15:42:05', '2025-07-11 15:42:05', '2025-07-11 15:42:05', 'active', NULL, NULL, '', '', '', 'active', NULL, '', ''),
(7, 'testing11', 'SUKABUMI', '081211122233', '', 5, 1, 5, 'aktif', '0000-00-00', '2025-07-16', NULL, 'testing121', 'testing', '10mbs', 'pppoe', '', '', '', '', '', 'no', 'yes', '2025-07-11 15:42:40', '2025-07-11 15:42:40', '2025-07-11 15:42:40', 'active', NULL, NULL, '', '', '', 'active', NULL, '', ''),
(8, 'dfgadfgadfg', 'Jl. Mawar No. 5, RT 01/RW 02', '081211122233', '', 5, 1, 5, 'aktif', '0000-00-00', '2025-07-18', NULL, 'testing12112', 'testing', '10mbs', 'pppoe', '', '', '', '', '', 'no', 'yes', '2025-07-11 15:50:20', '2025-07-11 15:50:20', '2025-07-11 15:50:20', 'active', NULL, NULL, '', '', '', 'active', NULL, '', ''),
(9, 'dfgadfgadfgwe', 'SUKABUMI', '081211122233', '', 5, 1, 5, 'aktif', '0000-00-00', '2025-07-15', NULL, 'testing1211212', 'testing', '10mbs', 'pppoe', '', '', '', '', '', 'no', 'yes', '2025-07-11 16:00:29', '2025-07-11 16:00:29', '2025-07-11 16:00:29', 'active', NULL, NULL, '', '', '', 'active', NULL, '', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ftth_odc`
--

CREATE TABLE `ftth_odc` (
  `id` int(11) NOT NULL,
  `nama_odc` varchar(100) NOT NULL,
  `pon_port_id` int(11) NOT NULL COMMENT 'PON Port yang terhubung dari OLT',
  `lokasi` varchar(200) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL COMMENT 'Koordinat latitude',
  `longitude` decimal(11,8) DEFAULT NULL COMMENT 'Koordinat longitude',
  `jumlah_port` int(11) NOT NULL DEFAULT 8 COMMENT 'Total port ODC untuk ODP',
  `port_tersedia` int(11) NOT NULL DEFAULT 8 COMMENT 'Port yang masih tersedia untuk ODP',
  `kapasitas_fiber` int(11) DEFAULT 24 COMMENT 'Kapasitas fiber dalam core',
  `jumlah_splitter` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','maintenance') NOT NULL DEFAULT 'active',
  `area_coverage` varchar(200) DEFAULT '',
  `keterangan` text DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ftth_odc`
--

INSERT INTO `ftth_odc` (`id`, `nama_odc`, `pon_port_id`, `lokasi`, `latitude`, `longitude`, `jumlah_port`, `port_tersedia`, `kapasitas_fiber`, `jumlah_splitter`, `status`, `area_coverage`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'ODC-1-TKL-1', 7, 'KABANDUNGAN', -6.79292039, 106.61897522, 8, 8, 8, 0, 'active', '', '', '2025-07-10 15:47:29', '2025-07-10 15:47:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ftth_odc_ports`
--

CREATE TABLE `ftth_odc_ports` (
  `id` int(11) NOT NULL,
  `odc_id` int(11) NOT NULL,
  `port_number` int(11) NOT NULL,
  `port_name` varchar(50) NOT NULL,
  `status` enum('available','connected','maintenance') NOT NULL DEFAULT 'available',
  `connected_odp_id` int(11) DEFAULT NULL COMMENT 'ODP yang terhubung ke port ini',
  `fiber_core` varchar(20) DEFAULT '',
  `keterangan` text DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ftth_odc_ports`
--

INSERT INTO `ftth_odc_ports` (`id`, `odc_id`, `port_number`, `port_name`, `status`, `connected_odp_id`, `fiber_core`, `keterangan`, `created_at`) VALUES
(1, 1, 1, 'ODC-L-1-01', 'connected', 1, '', '', '2025-07-10 15:47:29'),
(2, 1, 2, 'ODC-L-1-02', 'available', NULL, '', '', '2025-07-10 15:47:29'),
(3, 1, 3, 'ODC-L-1-03', 'available', NULL, '', '', '2025-07-10 15:47:29'),
(4, 1, 4, 'ODC-L-1-04', 'available', NULL, '', '', '2025-07-10 15:47:29'),
(5, 1, 5, 'ODC-L-1-05', 'available', NULL, '', '', '2025-07-10 15:47:29'),
(6, 1, 6, 'ODC-L-1-06', 'available', NULL, '', '', '2025-07-10 15:47:29'),
(7, 1, 7, 'ODC-L-1-07', 'available', NULL, '', '', '2025-07-10 15:47:29'),
(8, 1, 8, 'ODC-L-1-08', 'available', NULL, '', '', '2025-07-10 15:47:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ftth_odp`
--

CREATE TABLE `ftth_odp` (
  `id` int(11) NOT NULL,
  `nama_odp` varchar(100) NOT NULL,
  `odc_port_id` int(11) NOT NULL COMMENT 'Port ODC yang terhubung',
  `lokasi` varchar(200) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL COMMENT 'Koordinat latitude',
  `longitude` decimal(11,8) DEFAULT NULL COMMENT 'Koordinat longitude',
  `jumlah_port` int(11) NOT NULL DEFAULT 8 COMMENT 'Total port ODP untuk pelanggan',
  `port_tersedia` int(11) NOT NULL DEFAULT 8 COMMENT 'Port yang masih tersedia untuk pelanggan',
  `splitter_ratio` varchar(20) DEFAULT '1:8',
  `status` enum('active','inactive','maintenance') NOT NULL DEFAULT 'active',
  `jenis_odp` enum('aerial','underground') NOT NULL DEFAULT 'aerial',
  `area_coverage` varchar(200) DEFAULT '',
  `keterangan` text DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ftth_odp`
--

INSERT INTO `ftth_odp` (`id`, `nama_odp`, `odc_port_id`, `lokasi`, `latitude`, `longitude`, `jumlah_port`, `port_tersedia`, `splitter_ratio`, `status`, `jenis_odp`, `area_coverage`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'ODP INDOMART', 1, 'KABANDUNGAN', -6.79616924, 999.99999999, 8, 8, '1:8', 'active', 'aerial', '', '', '2025-07-10 15:50:34', '2025-07-10 15:50:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ftth_odp_ports`
--

CREATE TABLE `ftth_odp_ports` (
  `id` int(11) NOT NULL,
  `odp_id` int(11) NOT NULL,
  `port_number` int(11) NOT NULL,
  `port_name` varchar(50) NOT NULL,
  `status` enum('available','connected','maintenance') NOT NULL DEFAULT 'available',
  `connected_customer_id` int(11) DEFAULT NULL COMMENT 'ID pelanggan yang terhubung',
  `onu_id` varchar(20) DEFAULT '',
  `signal_rx` varchar(20) DEFAULT '',
  `signal_tx` varchar(20) DEFAULT '',
  `installation_date` date DEFAULT NULL,
  `technician` varchar(100) DEFAULT '',
  `keterangan` text DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ftth_odp_ports`
--

INSERT INTO `ftth_odp_ports` (`id`, `odp_id`, `port_number`, `port_name`, `status`, `connected_customer_id`, `onu_id`, `signal_rx`, `signal_tx`, `installation_date`, `technician`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'ODP-ART-01', 'available', NULL, '', '', '', NULL, '', '', '2025-07-10 15:50:34', '2025-07-10 15:50:34'),
(2, 1, 2, 'ODP-ART-02', 'available', NULL, '', '', '', NULL, '', '', '2025-07-10 15:50:34', '2025-07-10 15:50:34'),
(3, 1, 3, 'ODP-ART-03', 'available', NULL, '', '', '', NULL, '', '', '2025-07-10 15:50:34', '2025-07-10 15:50:34'),
(4, 1, 4, 'ODP-ART-04', 'available', NULL, '', '', '', NULL, '', '', '2025-07-10 15:50:34', '2025-07-10 15:50:34'),
(5, 1, 5, 'ODP-ART-05', 'available', NULL, '', '', '', NULL, '', '', '2025-07-10 15:50:34', '2025-07-10 15:50:34'),
(6, 1, 6, 'ODP-ART-06', 'available', NULL, '', '', '', NULL, '', '', '2025-07-10 15:50:34', '2025-07-10 15:50:34'),
(7, 1, 7, 'ODP-ART-07', 'available', NULL, '', '', '', NULL, '', '', '2025-07-10 15:50:34', '2025-07-10 15:50:34'),
(8, 1, 8, 'ODP-ART-08', 'available', NULL, '', '', '', NULL, '', '', '2025-07-10 15:50:34', '2025-07-10 15:50:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ftth_olt`
--

CREATE TABLE `ftth_olt` (
  `id` int(11) NOT NULL,
  `pop_id` int(11) DEFAULT NULL COMMENT 'ID POP tempat OLT berada',
  `nama_olt` varchar(100) NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `lokasi` varchar(200) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL COMMENT 'Koordinat latitude',
  `longitude` decimal(11,8) DEFAULT NULL COMMENT 'Koordinat longitude',
  `merk` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `jumlah_port_pon` int(11) NOT NULL DEFAULT 16,
  `port_tersedia` int(11) NOT NULL DEFAULT 16 COMMENT 'Port PON yang masih tersedia untuk ODC',
  `status` enum('active','inactive','maintenance') NOT NULL DEFAULT 'active',
  `snmp_community` varchar(50) DEFAULT 'public',
  `snmp_version` varchar(10) DEFAULT 'v2c',
  `keterangan` text DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ftth_olt`
--

INSERT INTO `ftth_olt` (`id`, `pop_id`, `nama_olt`, `ip_address`, `lokasi`, `latitude`, `longitude`, `merk`, `model`, `jumlah_port_pon`, `port_tersedia`, `status`, `snmp_community`, `snmp_version`, `keterangan`, `created_at`, `updated_at`) VALUES
(2, 2, 'OLT-SELATAN-001', '192.168.1.11', 'Area Selatan Jl. Raya Selatan No. 25', -6.25000000, 106.82000000, 'ZTE', 'C300', 8, 16, 'active', 'public', 'v2c', '', '2025-07-09 01:51:00', '2025-07-09 01:54:41'),
(3, 3, 'OLT-UTARA-001', '192.168.1.12', 'Area Utara Jl. Bypass Utara No. 45', -6.18000000, 106.87000000, 'Fiberhome', 'AN5516-01', 12, 16, 'active', 'public', 'v2c', '', '2025-07-09 01:51:00', '2025-07-09 01:54:41'),
(4, 5, 'OLT-1-TKL-4-PON', '192.168.11.1', 'KABANDUNGAN', -6.79292039, 106.61897522, 'Other', 'EPON', 4, 4, 'active', 'public', 'v2c', '', '2025-07-10 15:19:09', '2025-07-10 15:19:09'),
(5, 5, 'OLT-1-TKL-4-PON', '192.168.11.2', 'KABANDUNGAN', -6.79292039, 106.61897522, 'Fiberhome', 'EPON', 4, 4, 'active', 'public', 'v2c', '', '2025-07-10 15:31:42', '2025-07-10 15:31:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ftth_pon`
--

CREATE TABLE `ftth_pon` (
  `id` int(11) NOT NULL,
  `olt_id` int(11) NOT NULL,
  `port_number` varchar(20) NOT NULL,
  `port_name` varchar(50) NOT NULL,
  `status` enum('available','connected','maintenance') NOT NULL DEFAULT 'available',
  `connected_odc_id` int(11) DEFAULT NULL COMMENT 'ODC yang terhubung ke port ini',
  `max_distance` int(11) DEFAULT 20000 COMMENT 'Jarak maksimal dalam meter',
  `splitter_ratio` varchar(20) DEFAULT '1:32',
  `keterangan` text DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ftth_pon`
--

INSERT INTO `ftth_pon` (`id`, `olt_id`, `port_number`, `port_name`, `status`, `connected_odc_id`, `max_distance`, `splitter_ratio`, `keterangan`, `created_at`, `updated_at`) VALUES
(4, 2, '0/2/0', 'PON-SELATAN-1', 'available', NULL, 20000, '1:32', '', '2025-07-09 01:51:00', '2025-07-09 01:54:41'),
(5, 2, '0/2/1', 'PON-SELATAN-2', 'available', NULL, 20000, '1:32', '', '2025-07-09 01:51:00', '2025-07-09 01:54:41'),
(6, 3, '0/3/0', 'PON-UTARA-1', 'available', NULL, 20000, '1:32', '', '2025-07-09 01:51:00', '2025-07-09 01:54:41'),
(7, 4, '0/1/0', 'PON-PON-1', 'connected', 1, 20000, '1:32', '', '2025-07-10 15:19:09', '2025-07-10 15:47:29'),
(8, 4, '0/1/1', 'PON-PON-2', 'available', NULL, 20000, '1:32', '', '2025-07-10 15:19:09', '2025-07-10 15:19:09'),
(9, 4, '0/1/2', 'PON-PON-3', 'available', NULL, 20000, '1:32', '', '2025-07-10 15:19:09', '2025-07-10 15:19:09'),
(10, 4, '0/1/3', 'PON-PON-4', 'available', NULL, 20000, '1:32', '', '2025-07-10 15:19:09', '2025-07-10 15:19:09'),
(11, 5, '0/1/0', 'PON-PON-1', 'available', NULL, 20000, '1:32', '', '2025-07-10 15:31:42', '2025-07-10 15:31:42'),
(12, 5, '0/1/1', 'PON-PON-2', 'available', NULL, 20000, '1:32', '', '2025-07-10 15:31:42', '2025-07-10 15:31:42'),
(13, 5, '0/1/2', 'PON-PON-3', 'available', NULL, 20000, '1:32', '', '2025-07-10 15:31:42', '2025-07-10 15:31:42'),
(14, 5, '0/1/3', 'PON-PON-4', 'available', NULL, 20000, '1:32', '', '2025-07-10 15:31:42', '2025-07-10 15:31:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ftth_pop`
--

CREATE TABLE `ftth_pop` (
  `id` int(11) NOT NULL,
  `nama_pop` varchar(100) NOT NULL,
  `lokasi` varchar(200) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL COMMENT 'Koordinat latitude',
  `longitude` decimal(11,8) DEFAULT NULL COMMENT 'Koordinat longitude',
  `alamat_lengkap` text DEFAULT '',
  `kapasitas_olt` int(11) NOT NULL DEFAULT 5 COMMENT 'Maksimal OLT di POP ini',
  `jumlah_olt` int(11) DEFAULT 0 COMMENT 'Jumlah OLT yang sudah ada',
  `status` enum('active','inactive','maintenance') NOT NULL DEFAULT 'active',
  `pic_nama` varchar(100) DEFAULT '',
  `pic_telepon` varchar(50) DEFAULT '',
  `keterangan` text DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ftth_pop`
--

INSERT INTO `ftth_pop` (`id`, `nama_pop`, `lokasi`, `latitude`, `longitude`, `alamat_lengkap`, `kapasitas_olt`, `jumlah_olt`, `status`, `pic_nama`, `pic_telepon`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'POP-PUSAT', 'Jakarta Pusat', -6.20880000, 106.84560000, 'Gedung Telkom Jakarta Pusat, Jl. Sudirman No. 1, Jakarta Pusat', 5, 0, 'active', 'Ahmad Teknik', '08123456789', '', '2025-07-09 01:51:00', '2025-07-10 15:26:50'),
(2, 'POP-SELATAN', 'Jakarta Selatan', -6.25000000, 106.82000000, 'Gedung BTS Selatan, Jl. Raya Selatan No. 25, Jakarta Selatan', 3, 1, 'active', 'Budi Network', '08987654321', '', '2025-07-09 01:51:00', '2025-07-09 01:54:41'),
(3, 'POP-UTARA', 'Jakarta Utara', -6.18000000, 106.87000000, 'Tower Utara, Jl. Bypass Utara No. 45, Jakarta Utara', 4, 1, 'active', 'Citra Fiber', '08555666777', '', '2025-07-09 01:51:00', '2025-07-09 01:54:41'),
(5, 'POP-TANGKOLO1', 'KABANDUNGAN', -6.79292039, 106.61897522, 'KABANDUNGAN', 4, 2, 'active', 'DONIE', '0854111245', '', '2025-07-10 14:49:31', '2025-07-10 15:31:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotspot_profiles`
--

CREATE TABLE `hotspot_profiles` (
  `id_profile` int(11) NOT NULL,
  `nama_profile` varchar(100) NOT NULL,
  `profile_name` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `deskripsi` text DEFAULT '',
  `session_timeout` varchar(20) DEFAULT '',
  `idle_timeout` varchar(20) DEFAULT '',
  `keepalive_timeout` varchar(20) DEFAULT '30',
  `rate_limit_rx` varchar(20) DEFAULT '',
  `rate_limit_tx` varchar(20) DEFAULT '',
  `burst_limit_rx` varchar(20) DEFAULT '',
  `burst_limit_tx` varchar(20) DEFAULT '',
  `burst_threshold_rx` varchar(20) DEFAULT '',
  `burst_threshold_tx` varchar(20) DEFAULT '',
  `burst_time_rx` varchar(10) DEFAULT '',
  `burst_time_tx` varchar(10) DEFAULT '',
  `shared_users` tinyint(4) DEFAULT 1,
  `mac_cookie_timeout` varchar(20) DEFAULT '',
  `address_list` varchar(100) DEFAULT '',
  `incoming_filter` varchar(100) DEFAULT '',
  `outgoing_filter` varchar(100) DEFAULT '',
  `status_profile` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `sync_mikrotik` enum('yes','no') DEFAULT 'no',
  `last_sync` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotspot_sales`
--

CREATE TABLE `hotspot_sales` (
  `id_sale` int(11) NOT NULL,
  `id_user_hotspot` int(11) NOT NULL,
  `tanggal_jual` date NOT NULL,
  `harga_jual` decimal(10,2) NOT NULL,
  `nama_pembeli` varchar(100) DEFAULT NULL,
  `telepon_pembeli` varchar(50) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `id_user_penjual` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotspot_users`
--

CREATE TABLE `hotspot_users` (
  `id_user_hotspot` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `id_profile` int(11) DEFAULT NULL,
  `profile_name` varchar(100) DEFAULT '',
  `nama_voucher` varchar(100) DEFAULT '',
  `keterangan` text DEFAULT '',
  `uptime_limit` varchar(20) DEFAULT '',
  `bytes_in_limit` bigint(20) DEFAULT NULL,
  `bytes_out_limit` bigint(20) DEFAULT NULL,
  `status` enum('aktif','nonaktif','expired','used') NOT NULL DEFAULT 'aktif',
  `first_login` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `last_logout` timestamp NULL DEFAULT NULL,
  `uptime_used` varchar(20) DEFAULT '0',
  `bytes_in_used` bigint(20) DEFAULT 0,
  `bytes_out_used` bigint(20) DEFAULT 0,
  `mikrotik_comment` text DEFAULT '',
  `mikrotik_disabled` enum('yes','no') DEFAULT 'no',
  `batch_id` varchar(50) DEFAULT '',
  `batch_name` varchar(100) DEFAULT '',
  `sync_mikrotik` enum('yes','no') DEFAULT 'no',
  `last_sync` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `tabel_terkait` varchar(50) DEFAULT NULL,
  `id_data_terkait` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mikrotik_hotspot_profiles`
--

CREATE TABLE `mikrotik_hotspot_profiles` (
  `id_profile` int(11) NOT NULL,
  `nama_profile` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) DEFAULT 0.00,
  `rate_limit_rx_tx` varchar(20) DEFAULT '',
  `shared_users` tinyint(4) DEFAULT 1,
  `mac_cookie_timeout` varchar(20) DEFAULT '3d 00:00:00' COMMENT 'MAC cookie timeout duration',
  `address_list` varchar(100) DEFAULT '',
  `address_pool` varchar(100) DEFAULT NULL,
  `session_timeout` varchar(50) DEFAULT NULL,
  `idle_timeout` varchar(50) DEFAULT NULL,
  `expired_mode` enum('none','rem','ntf','remc','ntfc') DEFAULT 'none',
  `validity` varchar(20) DEFAULT NULL,
  `grace_period` varchar(20) DEFAULT '5m',
  `lock_user_enabled` enum('yes','no') DEFAULT 'no' COMMENT 'Enable/disable user lock to MAC address',
  `queue` varchar(100) DEFAULT '' COMMENT 'Queue name for bandwidth management',
  `status_profile` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `sync_mikrotik` enum('yes','no') DEFAULT 'no',
  `last_sync` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mikrotik_hotspot_profiles`
--

INSERT INTO `mikrotik_hotspot_profiles` (`id_profile`, `nama_profile`, `harga`, `selling_price`, `rate_limit_rx_tx`, `shared_users`, `mac_cookie_timeout`, `address_list`, `address_pool`, `session_timeout`, `idle_timeout`, `expired_mode`, `validity`, `grace_period`, `lock_user_enabled`, `queue`, `status_profile`, `sync_mikrotik`, `last_sync`, `created_at`, `updated_at`) VALUES
(7, 'VOUCHER-10000', 10000.00, 0.00, '2M/2M', 1, '3d 00:00:00', '', NULL, NULL, NULL, 'none', NULL, '5m', 'yes', '', 'aktif', 'yes', '2025-07-10 01:53:35', '2025-07-10 01:53:35', '2025-07-10 01:53:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `monitoring_pppoe`
--

CREATE TABLE `monitoring_pppoe` (
  `id_monitoring` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `mikrotik_username` varchar(100) NOT NULL,
  `session_id` varchar(50) DEFAULT '',
  `ip_address` varchar(20) DEFAULT '',
  `mac_address` varchar(20) DEFAULT '',
  `interface` varchar(50) DEFAULT '',
  `caller_id` varchar(50) DEFAULT '',
  `uptime` varchar(50) DEFAULT '',
  `bytes_in` bigint(20) DEFAULT 0,
  `bytes_out` bigint(20) DEFAULT 0,
  `packets_in` bigint(20) DEFAULT 0,
  `packets_out` bigint(20) DEFAULT 0,
  `session_start` timestamp NULL DEFAULT NULL,
  `session_end` timestamp NULL DEFAULT NULL,
  `disconnect_reason` varchar(100) DEFAULT '',
  `status` enum('active','disconnected') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `paket_internet`
--

CREATE TABLE `paket_internet` (
  `id_paket` int(11) NOT NULL,
  `nama_paket` varchar(100) NOT NULL,
  `profile_name` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `deskripsi` text DEFAULT '',
  `local_address` varchar(20) DEFAULT '',
  `remote_address` varchar(50) DEFAULT '',
  `session_timeout` varchar(20) DEFAULT '',
  `idle_timeout` varchar(20) DEFAULT '',
  `keepalive_timeout` varchar(20) DEFAULT '30',
  `rate_limit_rx` varchar(20) DEFAULT '',
  `rate_limit_tx` varchar(20) DEFAULT '',
  `burst_limit_rx` varchar(20) DEFAULT '',
  `burst_limit_tx` varchar(20) DEFAULT '',
  `burst_threshold_rx` varchar(20) DEFAULT '',
  `burst_threshold_tx` varchar(20) DEFAULT '',
  `burst_time_rx` varchar(10) DEFAULT '',
  `burst_time_tx` varchar(10) DEFAULT '',
  `priority` tinyint(1) DEFAULT 8,
  `parent_queue` varchar(50) DEFAULT '',
  `dns_server` varchar(100) DEFAULT '',
  `wins_server` varchar(50) DEFAULT '',
  `only_one` enum('yes','no') DEFAULT 'yes',
  `shared_users` tinyint(4) DEFAULT 1,
  `address_list` varchar(100) DEFAULT '',
  `incoming_filter` varchar(100) DEFAULT '',
  `outgoing_filter` varchar(100) DEFAULT '',
  `on_up` text DEFAULT '',
  `on_down` text DEFAULT '',
  `status_paket` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `sync_mikrotik` enum('yes','no') DEFAULT 'no',
  `last_sync` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `paket_internet`
--

INSERT INTO `paket_internet` (`id_paket`, `nama_paket`, `profile_name`, `harga`, `deskripsi`, `local_address`, `remote_address`, `session_timeout`, `idle_timeout`, `keepalive_timeout`, `rate_limit_rx`, `rate_limit_tx`, `burst_limit_rx`, `burst_limit_tx`, `burst_threshold_rx`, `burst_threshold_tx`, `burst_time_rx`, `burst_time_tx`, `priority`, `parent_queue`, `dns_server`, `wins_server`, `only_one`, `shared_users`, `address_list`, `incoming_filter`, `outgoing_filter`, `on_up`, `on_down`, `status_paket`, `sync_mikrotik`, `last_sync`, `created_at`, `updated_at`) VALUES
(5, '10MBS', '10mbs', 125000.00, '', '172.1.0.1', 'dhcp_pool0', '', '', '30', '10M', '10M', '', '', '', '', '', '', 8, '', '', '', 'yes', 1, '', '', '', '', '', 'aktif', 'yes', '2025-07-09 02:52:33', '2025-07-09 02:52:19', '2025-07-09 02:52:33');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_tagihan` varchar(20) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `jumlah_bayar` decimal(10,2) NOT NULL,
  `metode_bayar` varchar(50) DEFAULT '',
  `keterangan` text DEFAULT '',
  `id_user_pencatat` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan_perusahaan`
--

CREATE TABLE `pengaturan_perusahaan` (
  `id_pengaturan` int(11) NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat_perusahaan` text DEFAULT NULL,
  `telepon_perusahaan` varchar(50) DEFAULT NULL,
  `email_perusahaan` varchar(100) DEFAULT NULL,
  `bank_nama` varchar(100) DEFAULT NULL,
  `bank_atas_nama` varchar(100) DEFAULT NULL,
  `bank_no_rekening` varchar(50) DEFAULT NULL,
  `logo_perusahaan` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan_perusahaan`
--

INSERT INTO `pengaturan_perusahaan` (`id_pengaturan`, `nama_perusahaan`, `alamat_perusahaan`, `telepon_perusahaan`, `email_perusahaan`, `bank_nama`, `bank_atas_nama`, `bank_no_rekening`, `logo_perusahaan`, `updated_at`) VALUES
(1, 'PT. Area Near Urban Netsindo', 'Jl. Tirta Atmaja No. 34, Kp. Tangkolo RT. 03 RW. 01, Desa Tugubandung, Kec. Kabandungan', '085217197800', 'info@anunet.web.id', '', '', '', '../login.png', '2025-07-09 13:02:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id_pengeluaran` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `keterangan_lain` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `radcheck`
--

CREATE TABLE `radcheck` (
  `id` int(11) NOT NULL,
  `username` varchar(64) NOT NULL,
  `attribute` varchar(64) NOT NULL,
  `op` char(2) NOT NULL DEFAULT '==',
  `value` varchar(253) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `mikrotik_ip` varchar(50) NOT NULL,
  `mikrotik_user` varchar(50) NOT NULL,
  `mikrotik_pass` varchar(100) NOT NULL,
  `mikrotik_port` int(11) NOT NULL DEFAULT 8728
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `system_settings`
--

INSERT INTO `system_settings` (`id`, `mikrotik_ip`, `mikrotik_user`, `mikrotik_pass`, `mikrotik_port`) VALUES
(1, '192.168.88.1', 'admin', 'admin', 8728);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tagihan`
--

CREATE TABLE `tagihan` (
  `id_tagihan` varchar(20) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `bulan_tagihan` int(11) NOT NULL,
  `tahun_tagihan` int(11) NOT NULL,
  `jumlah_tagihan` decimal(10,2) NOT NULL,
  `tgl_jatuh_tempo` date NOT NULL,
  `status_tagihan` enum('belum_bayar','sudah_bayar','terlambat') NOT NULL DEFAULT 'belum_bayar',
  `deskripsi` text DEFAULT '',
  `auto_generated` enum('yes','no') DEFAULT 'no',
  `generated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tagihan`
--

INSERT INTO `tagihan` (`id_tagihan`, `id_pelanggan`, `bulan_tagihan`, `tahun_tagihan`, `jumlah_tagihan`, `tgl_jatuh_tempo`, `status_tagihan`, `deskripsi`, `auto_generated`, `generated_by`, `created_at`, `updated_at`) VALUES
('INV2025070001', 2, 7, 2025, 125000.00, '2025-07-15', 'belum_bayar', 'Tagihan perdana pelanggan JARWOTI - July 2025', 'no', NULL, '2025-07-10 06:48:06', '2025-07-10 06:48:06'),
('INV2025070002', 3, 7, 2025, 125000.00, '2025-07-10', 'terlambat', 'Tagihan perdana pelanggan JARWOTIR - July 2025', 'no', NULL, '2025-07-10 16:02:28', '2025-07-11 15:37:19'),
('INV2025070003', 4, 7, 2025, 125000.00, '2025-08-11', 'belum_bayar', 'Tagihan perdana pelanggan IBU MAEY - July 2025', 'no', NULL, '2025-07-11 15:37:18', '2025-07-11 15:37:18'),
('INV2025070004', 5, 7, 2025, 125000.00, '2025-07-16', 'belum_bayar', 'Tagihan perdana pelanggan JARWOTIR2 - July 2025', 'no', NULL, '2025-07-11 15:40:13', '2025-07-11 15:40:13'),
('INV2025070005', 6, 7, 2025, 125000.00, '2025-07-25', 'belum_bayar', 'Tagihan perdana pelanggan testing - July 2025', 'no', NULL, '2025-07-11 15:42:05', '2025-07-11 15:42:05'),
('INV2025070006', 7, 7, 2025, 125000.00, '2025-07-16', 'belum_bayar', 'Tagihan perdana pelanggan testing11 - July 2025', 'no', NULL, '2025-07-11 15:42:40', '2025-07-11 15:42:40'),
('INV2025070007', 8, 7, 2025, 125000.00, '2025-07-18', 'belum_bayar', 'Tagihan perdana pelanggan dfgadfgadfg - July 2025', 'no', NULL, '2025-07-11 15:50:20', '2025-07-11 15:50:20'),
('INV2025070008', 9, 7, 2025, 125000.00, '2025-07-15', 'belum_bayar', 'Tagihan perdana pelanggan dfgadfgadfgwe - July 2025', 'no', NULL, '2025-07-11 16:00:29', '2025-07-11 16:00:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_lain`
--

CREATE TABLE `transaksi_lain` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` enum('pemasukan','pengeluaran') NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `level` enum('admin','operator') NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `level`, `status`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', '17c4520f6cfd1ab53d8745e84681eb49', 'Administrator', 'admin', 'aktif', '2025-07-09 01:51:00', '2025-07-09 01:51:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `voucher_history`
--

CREATE TABLE `voucher_history` (
  `id` int(11) NOT NULL,
  `profile_name` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `total_nilai` decimal(15,2) NOT NULL,
  `batch_id` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `voucher_temp`
--

CREATE TABLE `voucher_temp` (
  `id` int(11) NOT NULL,
  `batch_id` varchar(10) NOT NULL,
  `encrypted_data` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_dashboard_summary`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_dashboard_summary` (
`total_pelanggan_aktif` bigint(21)
,`total_pelanggan_nonaktif` bigint(21)
,`total_pelanggan_isolir` bigint(21)
,`tagihan_belum_bayar_bulan_ini` bigint(21)
,`tagihan_sudah_bayar_bulan_ini` bigint(21)
,`total_piutang` decimal(32,2)
,`pendapatan_bulan_ini` decimal(32,2)
,`total_online_sekarang` bigint(21)
,`pending_auto_invoices` bigint(21)
,`upcoming_auto_invoices` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_ftth_customer_connections`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_ftth_customer_connections` (
`id_pelanggan` int(11)
,`nama_pelanggan` varchar(100)
,`alamat_pelanggan` text
,`telepon_pelanggan` varchar(50)
,`ftth_status` enum('active','inactive','maintenance')
,`installation_date` date
,`technician` varchar(100)
,`nama_pop` varchar(100)
,`nama_olt` varchar(100)
,`ip_address` varchar(15)
,`pon_port` varchar(50)
,`nama_odc` varchar(100)
,`nama_odp` varchar(100)
,`customer_port` varchar(50)
,`onu_id` varchar(20)
,`signal_rx` varchar(20)
,`signal_tx` varchar(20)
,`nama_paket` varchar(100)
,`rate_limit_rx` varchar(20)
,`rate_limit_tx` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_ftth_infrastructure_summary`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_ftth_infrastructure_summary` (
`total_pop_aktif` bigint(21)
,`total_olt_aktif` bigint(21)
,`total_odc_aktif` bigint(21)
,`total_odp_aktif` bigint(21)
,`total_pon_ports` decimal(32,0)
,`pon_ports_tersedia` decimal(32,0)
,`odp_ports_tersedia` bigint(21)
,`odp_ports_terpakai` bigint(21)
,`pelanggan_ftth_aktif` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_ftth_topology`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_ftth_topology` (
`nama_pop` varchar(100)
,`pop_lokasi` varchar(200)
,`nama_olt` varchar(100)
,`ip_address` varchar(15)
,`olt_lokasi` varchar(200)
,`pon_port` varchar(50)
,`nama_odc` varchar(100)
,`odc_lokasi` varchar(200)
,`odc_ports_tersedia` int(11)
,`nama_odp` varchar(100)
,`odp_lokasi` varchar(200)
,`odp_ports_tersedia` int(11)
,`nama_pelanggan` varchar(100)
,`ftth_status` enum('active','inactive','maintenance')
,`customer_port` varchar(50)
,`onu_id` varchar(20)
,`signal_rx` varchar(20)
,`signal_tx` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_hotspot_sales_report`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_hotspot_sales_report` (
`id_sale` int(11)
,`tanggal_jual` date
,`harga_jual` decimal(10,2)
,`nama_pembeli` varchar(100)
,`telepon_pembeli` varchar(50)
,`keterangan_penjualan` text
,`voucher_username` varchar(100)
,`nama_voucher` varchar(100)
,`nama_profile` varchar(100)
,`harga_profile` decimal(10,2)
,`nama_penjual` varchar(100)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_hotspot_summary`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_hotspot_summary` (
`total_voucher_aktif` bigint(21)
,`total_voucher_terpakai` bigint(21)
,`total_voucher_expired` bigint(21)
,`total_voucher_nonaktif` bigint(21)
,`total_profile_aktif` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_invoice_auto_candidates`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_invoice_auto_candidates` (
`id_pelanggan` int(11)
,`nama_pelanggan` varchar(100)
,`telepon_pelanggan` varchar(50)
,`tgl_expired` date
,`invoice_due_date` date
,`harga_paket` decimal(10,2)
,`nama_paket` varchar(100)
,`days_until_invoice` int(7)
,`invoice_status` varchar(6)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_invoice_metrics`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_invoice_metrics` (
`total_tagihan` bigint(21)
,`belum_bayar` decimal(22,0)
,`sudah_bayar` decimal(22,0)
,`terlambat` decimal(22,0)
,`overdue` decimal(22,0)
,`due_today` decimal(22,0)
,`total_belum_bayar` decimal(32,2)
,`total_sudah_bayar` decimal(32,2)
,`total_overdue` decimal(32,2)
,`auto_generated_count` decimal(22,0)
,`manual_generated_count` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_laporan_pembayaran`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_laporan_pembayaran` (
`id_pembayaran` int(11)
,`id_pelanggan` int(11)
,`nama_pelanggan` varchar(100)
,`alamat_pelanggan` text
,`telepon_pelanggan` varchar(50)
,`bulan_tagihan` int(11)
,`tahun_tagihan` int(11)
,`tanggal_bayar` date
,`jumlah_bayar` decimal(10,2)
,`metode_bayar` varchar(50)
,`keterangan` text
,`pencatat` varchar(100)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_laporan_tagihan`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_laporan_tagihan` (
`id_tagihan` varchar(20)
,`id_pelanggan` int(11)
,`nama_pelanggan` varchar(100)
,`alamat_pelanggan` text
,`telepon_pelanggan` varchar(50)
,`nama_paket` varchar(100)
,`profile_name` varchar(100)
,`kecepatan` varchar(41)
,`bulan_tagihan` int(11)
,`tahun_tagihan` int(11)
,`jumlah_tagihan` decimal(10,2)
,`tgl_jatuh_tempo` date
,`status_tagihan` enum('belum_bayar','sudah_bayar','terlambat')
,`umur_tagihan` int(7)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_monitoring_aktif`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_monitoring_aktif` (
`id_monitoring` int(11)
,`id_pelanggan` int(11)
,`nama_pelanggan` varchar(100)
,`alamat_pelanggan` text
,`nama_paket` varchar(100)
,`profile_name` varchar(100)
,`kecepatan` varchar(41)
,`mikrotik_username` varchar(100)
,`ip_address` varchar(20)
,`mac_address` varchar(20)
,`interface` varchar(50)
,`uptime` varchar(50)
,`bytes_in` bigint(20)
,`bytes_out` bigint(20)
,`session_start` timestamp
,`last_update` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_paket_internet_safe`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_paket_internet_safe` (
`id_paket` int(11)
,`nama_paket` varchar(100)
,`profile_name` varchar(100)
,`harga` decimal(10,2)
,`deskripsi` mediumtext
,`local_address` varchar(20)
,`remote_address` varchar(50)
,`session_timeout` varchar(20)
,`idle_timeout` varchar(20)
,`keepalive_timeout` varchar(20)
,`rate_limit_rx` varchar(20)
,`rate_limit_tx` varchar(20)
,`burst_limit_rx` varchar(20)
,`burst_limit_tx` varchar(20)
,`burst_threshold_rx` varchar(20)
,`burst_threshold_tx` varchar(20)
,`burst_time_rx` varchar(10)
,`burst_time_tx` varchar(10)
,`priority` tinyint(1)
,`parent_queue` varchar(50)
,`dns_server` varchar(100)
,`wins_server` varchar(50)
,`only_one` enum('yes','no')
,`shared_users` tinyint(4)
,`address_list` varchar(100)
,`incoming_filter` varchar(100)
,`outgoing_filter` varchar(100)
,`on_up` mediumtext
,`on_down` mediumtext
,`status_paket` enum('aktif','nonaktif')
,`sync_mikrotik` enum('yes','no')
,`last_sync` timestamp
,`created_at` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Struktur untuk view `v_dashboard_summary`
--
DROP TABLE IF EXISTS `v_dashboard_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_dashboard_summary`  AS SELECT (select count(0) from `data_pelanggan` where `data_pelanggan`.`status_aktif` = 'aktif') AS `total_pelanggan_aktif`, (select count(0) from `data_pelanggan` where `data_pelanggan`.`status_aktif` = 'nonaktif') AS `total_pelanggan_nonaktif`, (select count(0) from `data_pelanggan` where `data_pelanggan`.`status_aktif` = 'isolir') AS `total_pelanggan_isolir`, (select count(0) from `tagihan` where `tagihan`.`bulan_tagihan` = month(curdate()) and `tagihan`.`tahun_tagihan` = year(curdate()) and `tagihan`.`status_tagihan` in ('belum_bayar','terlambat')) AS `tagihan_belum_bayar_bulan_ini`, (select count(0) from `tagihan` where `tagihan`.`bulan_tagihan` = month(curdate()) and `tagihan`.`tahun_tagihan` = year(curdate()) and `tagihan`.`status_tagihan` = 'sudah_bayar') AS `tagihan_sudah_bayar_bulan_ini`, (select coalesce(sum(`tagihan`.`jumlah_tagihan`),0) from `tagihan` where `tagihan`.`status_tagihan` in ('belum_bayar','terlambat')) AS `total_piutang`, (select coalesce(sum(`pembayaran`.`jumlah_bayar`),0) from `pembayaran` where month(`pembayaran`.`tanggal_bayar`) = month(curdate()) and year(`pembayaran`.`tanggal_bayar`) = year(curdate())) AS `pendapatan_bulan_ini`, (select count(0) from `monitoring_pppoe` where `monitoring_pppoe`.`status` = 'active') AS `total_online_sekarang`, (select count(0) from `v_invoice_auto_candidates` where `v_invoice_auto_candidates`.`invoice_status` = 'READY') AS `pending_auto_invoices`, (select count(0) from `v_invoice_auto_candidates` where `v_invoice_auto_candidates`.`invoice_status` = 'SOON') AS `upcoming_auto_invoices` ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_ftth_customer_connections`
--
DROP TABLE IF EXISTS `v_ftth_customer_connections`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_ftth_customer_connections`  AS SELECT `dp`.`id_pelanggan` AS `id_pelanggan`, `dp`.`nama_pelanggan` AS `nama_pelanggan`, `dp`.`alamat_pelanggan` AS `alamat_pelanggan`, `dp`.`telepon_pelanggan` AS `telepon_pelanggan`, `dp`.`ftth_status` AS `ftth_status`, `dp`.`installation_date` AS `installation_date`, `dp`.`technician` AS `technician`, `pop`.`nama_pop` AS `nama_pop`, `olt`.`nama_olt` AS `nama_olt`, `olt`.`ip_address` AS `ip_address`, `pon`.`port_name` AS `pon_port`, `odc`.`nama_odc` AS `nama_odc`, `odp`.`nama_odp` AS `nama_odp`, `odpp`.`port_name` AS `customer_port`, `odpp`.`onu_id` AS `onu_id`, `odpp`.`signal_rx` AS `signal_rx`, `odpp`.`signal_tx` AS `signal_tx`, `pi`.`nama_paket` AS `nama_paket`, `pi`.`rate_limit_rx` AS `rate_limit_rx`, `pi`.`rate_limit_tx` AS `rate_limit_tx` FROM ((((((((`data_pelanggan` `dp` join `ftth_odp_ports` `odpp` on(`dp`.`odp_port_id` = `odpp`.`id`)) join `ftth_odp` `odp` on(`odpp`.`odp_id` = `odp`.`id`)) join `ftth_odc_ports` `odcp` on(`odp`.`odc_port_id` = `odcp`.`id`)) join `ftth_odc` `odc` on(`odcp`.`odc_id` = `odc`.`id`)) join `ftth_pon` `pon` on(`odc`.`pon_port_id` = `pon`.`id`)) join `ftth_olt` `olt` on(`pon`.`olt_id` = `olt`.`id`)) join `ftth_pop` `pop` on(`olt`.`pop_id` = `pop`.`id`)) left join `paket_internet` `pi` on(`dp`.`id_paket` = `pi`.`id_paket`)) WHERE `dp`.`odp_port_id` is not null ORDER BY `pop`.`nama_pop` ASC, `olt`.`nama_olt` ASC, `odc`.`nama_odc` ASC, `odp`.`nama_odp` ASC, `odpp`.`port_number` ASC ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_ftth_infrastructure_summary`
--
DROP TABLE IF EXISTS `v_ftth_infrastructure_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_ftth_infrastructure_summary`  AS SELECT (select count(0) from `ftth_pop` where `ftth_pop`.`status` = 'active') AS `total_pop_aktif`, (select count(0) from `ftth_olt` where `ftth_olt`.`status` = 'active') AS `total_olt_aktif`, (select count(0) from `ftth_odc` where `ftth_odc`.`status` = 'active') AS `total_odc_aktif`, (select count(0) from `ftth_odp` where `ftth_odp`.`status` = 'active') AS `total_odp_aktif`, (select coalesce(sum(`ftth_olt`.`jumlah_port_pon`),0) from `ftth_olt`) AS `total_pon_ports`, (select coalesce(sum(`ftth_olt`.`port_tersedia`),0) from `ftth_olt`) AS `pon_ports_tersedia`, (select count(0) from `ftth_odp_ports` where `ftth_odp_ports`.`status` = 'available') AS `odp_ports_tersedia`, (select count(0) from `ftth_odp_ports` where `ftth_odp_ports`.`status` = 'connected') AS `odp_ports_terpakai`, (select count(0) from `data_pelanggan` where `data_pelanggan`.`ftth_status` = 'active') AS `pelanggan_ftth_aktif` ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_ftth_topology`
--
DROP TABLE IF EXISTS `v_ftth_topology`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_ftth_topology`  AS SELECT `pop`.`nama_pop` AS `nama_pop`, `pop`.`lokasi` AS `pop_lokasi`, `olt`.`nama_olt` AS `nama_olt`, `olt`.`ip_address` AS `ip_address`, `olt`.`lokasi` AS `olt_lokasi`, `pon`.`port_name` AS `pon_port`, `odc`.`nama_odc` AS `nama_odc`, `odc`.`lokasi` AS `odc_lokasi`, `odc`.`port_tersedia` AS `odc_ports_tersedia`, `odp`.`nama_odp` AS `nama_odp`, `odp`.`lokasi` AS `odp_lokasi`, `odp`.`port_tersedia` AS `odp_ports_tersedia`, `dp`.`nama_pelanggan` AS `nama_pelanggan`, `dp`.`ftth_status` AS `ftth_status`, `odpp`.`port_name` AS `customer_port`, `odpp`.`onu_id` AS `onu_id`, `odpp`.`signal_rx` AS `signal_rx`, `odpp`.`signal_tx` AS `signal_tx` FROM (((((((`ftth_pop` `pop` left join `ftth_olt` `olt` on(`pop`.`id` = `olt`.`pop_id`)) left join `ftth_pon` `pon` on(`olt`.`id` = `pon`.`olt_id`)) left join `ftth_odc` `odc` on(`pon`.`id` = `odc`.`pon_port_id`)) left join `ftth_odc_ports` `odcp` on(`odc`.`id` = `odcp`.`odc_id`)) left join `ftth_odp` `odp` on(`odcp`.`id` = `odp`.`odc_port_id`)) left join `ftth_odp_ports` `odpp` on(`odp`.`id` = `odpp`.`odp_id`)) left join `data_pelanggan` `dp` on(`odpp`.`connected_customer_id` = `dp`.`id_pelanggan`)) ORDER BY `pop`.`nama_pop` ASC, `olt`.`nama_olt` ASC, `pon`.`port_name` ASC, `odc`.`nama_odc` ASC, `odp`.`nama_odp` ASC ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_hotspot_sales_report`
--
DROP TABLE IF EXISTS `v_hotspot_sales_report`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_hotspot_sales_report`  AS SELECT `hs`.`id_sale` AS `id_sale`, `hs`.`tanggal_jual` AS `tanggal_jual`, `hs`.`harga_jual` AS `harga_jual`, `hs`.`nama_pembeli` AS `nama_pembeli`, `hs`.`telepon_pembeli` AS `telepon_pembeli`, `hs`.`keterangan` AS `keterangan_penjualan`, `hu`.`username` AS `voucher_username`, `hu`.`nama_voucher` AS `nama_voucher`, `hp`.`nama_profile` AS `nama_profile`, `hp`.`harga` AS `harga_profile`, `u`.`nama_lengkap` AS `nama_penjual`, `hs`.`created_at` AS `created_at` FROM (((`hotspot_sales` `hs` left join `hotspot_users` `hu` on(`hs`.`id_user_hotspot` = `hu`.`id_user_hotspot`)) left join `hotspot_profiles` `hp` on(`hu`.`id_profile` = `hp`.`id_profile`)) left join `users` `u` on(`hs`.`id_user_penjual` = `u`.`id_user`)) ORDER BY `hs`.`tanggal_jual` DESC ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_hotspot_summary`
--
DROP TABLE IF EXISTS `v_hotspot_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_hotspot_summary`  AS SELECT (select count(0) from `hotspot_users` where `hotspot_users`.`status` = 'aktif') AS `total_voucher_aktif`, (select count(0) from `hotspot_users` where `hotspot_users`.`status` = 'used') AS `total_voucher_terpakai`, (select count(0) from `hotspot_users` where `hotspot_users`.`status` = 'expired') AS `total_voucher_expired`, (select count(0) from `hotspot_users` where `hotspot_users`.`status` = 'nonaktif') AS `total_voucher_nonaktif`, (select count(0) from `hotspot_profiles` where `hotspot_profiles`.`status_profile` = 'aktif') AS `total_profile_aktif` ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_invoice_auto_candidates`
--
DROP TABLE IF EXISTS `v_invoice_auto_candidates`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_invoice_auto_candidates`  AS SELECT `dp`.`id_pelanggan` AS `id_pelanggan`, `dp`.`nama_pelanggan` AS `nama_pelanggan`, `dp`.`telepon_pelanggan` AS `telepon_pelanggan`, `dp`.`tgl_expired` AS `tgl_expired`, `dp`.`tgl_expired`- interval 10 day AS `invoice_due_date`, coalesce(`pi`.`harga`,0) AS `harga_paket`, coalesce(`pi`.`nama_paket`,'Tidak ada paket') AS `nama_paket`, to_days(`dp`.`tgl_expired` - interval 10 day) - to_days(curdate()) AS `days_until_invoice`, CASE WHEN `dp`.`tgl_expired` - interval 10 day <= curdate() THEN 'READY' WHEN `dp`.`tgl_expired` - interval 10 day <= curdate() + interval 3 day THEN 'SOON' ELSE 'FUTURE' END AS `invoice_status` FROM ((`data_pelanggan` `dp` left join `paket_internet` `pi` on(`dp`.`id_paket` = `pi`.`id_paket`)) left join `tagihan` `t` on(`t`.`id_pelanggan` = `dp`.`id_pelanggan` and `t`.`tgl_jatuh_tempo` = `dp`.`tgl_expired` - interval 10 day)) WHERE `dp`.`tgl_expired` is not null AND `dp`.`status_aktif` = 'aktif' AND `t`.`id_tagihan` is null ORDER BY `dp`.`tgl_expired` ASC ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_invoice_metrics`
--
DROP TABLE IF EXISTS `v_invoice_metrics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_invoice_metrics`  AS SELECT count(0) AS `total_tagihan`, sum(case when `t`.`status_tagihan` = 'belum_bayar' then 1 else 0 end) AS `belum_bayar`, sum(case when `t`.`status_tagihan` = 'sudah_bayar' then 1 else 0 end) AS `sudah_bayar`, sum(case when `t`.`status_tagihan` = 'terlambat' then 1 else 0 end) AS `terlambat`, sum(case when `t`.`tgl_jatuh_tempo` < curdate() and `t`.`status_tagihan` <> 'sudah_bayar' then 1 else 0 end) AS `overdue`, sum(case when `t`.`tgl_jatuh_tempo` = curdate() and `t`.`status_tagihan` <> 'sudah_bayar' then 1 else 0 end) AS `due_today`, sum(case when `t`.`status_tagihan` = 'belum_bayar' then `t`.`jumlah_tagihan` else 0 end) AS `total_belum_bayar`, sum(case when `t`.`status_tagihan` = 'sudah_bayar' then `t`.`jumlah_tagihan` else 0 end) AS `total_sudah_bayar`, sum(case when `t`.`tgl_jatuh_tempo` < curdate() and `t`.`status_tagihan` <> 'sudah_bayar' then `t`.`jumlah_tagihan` else 0 end) AS `total_overdue`, sum(case when `t`.`auto_generated` = 'yes' then 1 else 0 end) AS `auto_generated_count`, sum(case when `t`.`auto_generated` = 'no' or `t`.`auto_generated` is null then 1 else 0 end) AS `manual_generated_count` FROM (`tagihan` `t` join `data_pelanggan` `dp` on(`t`.`id_pelanggan` = `dp`.`id_pelanggan`)) WHERE `dp`.`tgl_expired` is null OR `dp`.`tgl_expired` >= curdate() - interval 10 day ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_laporan_pembayaran`
--
DROP TABLE IF EXISTS `v_laporan_pembayaran`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_laporan_pembayaran`  AS SELECT `pb`.`id_pembayaran` AS `id_pembayaran`, `p`.`id_pelanggan` AS `id_pelanggan`, `p`.`nama_pelanggan` AS `nama_pelanggan`, `p`.`alamat_pelanggan` AS `alamat_pelanggan`, `p`.`telepon_pelanggan` AS `telepon_pelanggan`, `t`.`bulan_tagihan` AS `bulan_tagihan`, `t`.`tahun_tagihan` AS `tahun_tagihan`, `pb`.`tanggal_bayar` AS `tanggal_bayar`, `pb`.`jumlah_bayar` AS `jumlah_bayar`, `pb`.`metode_bayar` AS `metode_bayar`, `pb`.`keterangan` AS `keterangan`, `u`.`nama_lengkap` AS `pencatat`, `pb`.`created_at` AS `created_at` FROM (((`pembayaran` `pb` join `data_pelanggan` `p` on(`pb`.`id_pelanggan` = `p`.`id_pelanggan`)) join `tagihan` `t` on(`pb`.`id_tagihan` = `t`.`id_tagihan`)) left join `users` `u` on(`pb`.`id_user_pencatat` = `u`.`id_user`)) ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_laporan_tagihan`
--
DROP TABLE IF EXISTS `v_laporan_tagihan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_laporan_tagihan`  AS SELECT `t`.`id_tagihan` AS `id_tagihan`, `p`.`id_pelanggan` AS `id_pelanggan`, `p`.`nama_pelanggan` AS `nama_pelanggan`, `p`.`alamat_pelanggan` AS `alamat_pelanggan`, `p`.`telepon_pelanggan` AS `telepon_pelanggan`, `pi`.`nama_paket` AS `nama_paket`, `pi`.`profile_name` AS `profile_name`, concat(`pi`.`rate_limit_rx`,'/',`pi`.`rate_limit_tx`) AS `kecepatan`, `t`.`bulan_tagihan` AS `bulan_tagihan`, `t`.`tahun_tagihan` AS `tahun_tagihan`, `t`.`jumlah_tagihan` AS `jumlah_tagihan`, `t`.`tgl_jatuh_tempo` AS `tgl_jatuh_tempo`, `t`.`status_tagihan` AS `status_tagihan`, to_days(curdate()) - to_days(`t`.`tgl_jatuh_tempo`) AS `umur_tagihan`, `t`.`created_at` AS `created_at` FROM ((`tagihan` `t` join `data_pelanggan` `p` on(`t`.`id_pelanggan` = `p`.`id_pelanggan`)) left join `paket_internet` `pi` on(`p`.`id_paket` = `pi`.`id_paket`)) ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_monitoring_aktif`
--
DROP TABLE IF EXISTS `v_monitoring_aktif`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_monitoring_aktif`  AS SELECT `m`.`id_monitoring` AS `id_monitoring`, `p`.`id_pelanggan` AS `id_pelanggan`, `p`.`nama_pelanggan` AS `nama_pelanggan`, `p`.`alamat_pelanggan` AS `alamat_pelanggan`, `pi`.`nama_paket` AS `nama_paket`, `pi`.`profile_name` AS `profile_name`, concat(`pi`.`rate_limit_rx`,'/',`pi`.`rate_limit_tx`) AS `kecepatan`, `m`.`mikrotik_username` AS `mikrotik_username`, `m`.`ip_address` AS `ip_address`, `m`.`mac_address` AS `mac_address`, `m`.`interface` AS `interface`, `m`.`uptime` AS `uptime`, `m`.`bytes_in` AS `bytes_in`, `m`.`bytes_out` AS `bytes_out`, `m`.`session_start` AS `session_start`, `m`.`updated_at` AS `last_update` FROM ((`monitoring_pppoe` `m` join `data_pelanggan` `p` on(`m`.`id_pelanggan` = `p`.`id_pelanggan`)) left join `paket_internet` `pi` on(`p`.`id_paket` = `pi`.`id_paket`)) WHERE `m`.`status` = 'active' ;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_paket_internet_safe`
--
DROP TABLE IF EXISTS `v_paket_internet_safe`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_paket_internet_safe`  AS SELECT `paket_internet`.`id_paket` AS `id_paket`, `paket_internet`.`nama_paket` AS `nama_paket`, `paket_internet`.`profile_name` AS `profile_name`, `paket_internet`.`harga` AS `harga`, coalesce(`paket_internet`.`deskripsi`,'') AS `deskripsi`, coalesce(`paket_internet`.`local_address`,'') AS `local_address`, coalesce(`paket_internet`.`remote_address`,'') AS `remote_address`, coalesce(`paket_internet`.`session_timeout`,'') AS `session_timeout`, coalesce(`paket_internet`.`idle_timeout`,'') AS `idle_timeout`, `paket_internet`.`keepalive_timeout` AS `keepalive_timeout`, coalesce(`paket_internet`.`rate_limit_rx`,'') AS `rate_limit_rx`, coalesce(`paket_internet`.`rate_limit_tx`,'') AS `rate_limit_tx`, coalesce(`paket_internet`.`burst_limit_rx`,'') AS `burst_limit_rx`, coalesce(`paket_internet`.`burst_limit_tx`,'') AS `burst_limit_tx`, coalesce(`paket_internet`.`burst_threshold_rx`,'') AS `burst_threshold_rx`, coalesce(`paket_internet`.`burst_threshold_tx`,'') AS `burst_threshold_tx`, coalesce(`paket_internet`.`burst_time_rx`,'') AS `burst_time_rx`, coalesce(`paket_internet`.`burst_time_tx`,'') AS `burst_time_tx`, `paket_internet`.`priority` AS `priority`, coalesce(`paket_internet`.`parent_queue`,'') AS `parent_queue`, coalesce(`paket_internet`.`dns_server`,'') AS `dns_server`, coalesce(`paket_internet`.`wins_server`,'') AS `wins_server`, `paket_internet`.`only_one` AS `only_one`, `paket_internet`.`shared_users` AS `shared_users`, coalesce(`paket_internet`.`address_list`,'') AS `address_list`, coalesce(`paket_internet`.`incoming_filter`,'') AS `incoming_filter`, coalesce(`paket_internet`.`outgoing_filter`,'') AS `outgoing_filter`, coalesce(`paket_internet`.`on_up`,'') AS `on_up`, coalesce(`paket_internet`.`on_down`,'') AS `on_down`, `paket_internet`.`status_paket` AS `status_paket`, `paket_internet`.`sync_mikrotik` AS `sync_mikrotik`, `paket_internet`.`last_sync` AS `last_sync`, `paket_internet`.`created_at` AS `created_at`, `paket_internet`.`updated_at` AS `updated_at` FROM `paket_internet` ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_timestamp` (`timestamp`);

--
-- Indeks untuk tabel `data_pelanggan`
--
ALTER TABLE `data_pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD UNIQUE KEY `mikrotik_username` (`mikrotik_username`),
  ADD KEY `idx_paket` (`id_paket`),
  ADD KEY `idx_status` (`status_aktif`),
  ADD KEY `idx_nama` (`nama_pelanggan`),
  ADD KEY `idx_sync` (`sync_mikrotik`),
  ADD KEY `idx_odp_port` (`odp_port_id`),
  ADD KEY `fk_pelanggan_odp` (`odp_id`),
  ADD KEY `fk_pelanggan_pop` (`pop_id`);

--
-- Indeks untuk tabel `ftth_odc`
--
ALTER TABLE `ftth_odc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pon_port` (`pon_port_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indeks untuk tabel `ftth_odc_ports`
--
ALTER TABLE `ftth_odc_ports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_odc` (`odc_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indeks untuk tabel `ftth_odp`
--
ALTER TABLE `ftth_odp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_odc_port` (`odc_port_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indeks untuk tabel `ftth_odp_ports`
--
ALTER TABLE `ftth_odp_ports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_odp` (`odp_id`),
  ADD KEY `idx_customer` (`connected_customer_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indeks untuk tabel `ftth_olt`
--
ALTER TABLE `ftth_olt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_pop` (`pop_id`);

--
-- Indeks untuk tabel `ftth_pon`
--
ALTER TABLE `ftth_pon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_olt` (`olt_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indeks untuk tabel `ftth_pop`
--
ALTER TABLE `ftth_pop`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indeks untuk tabel `hotspot_profiles`
--
ALTER TABLE `hotspot_profiles`
  ADD PRIMARY KEY (`id_profile`),
  ADD UNIQUE KEY `nama_profile` (`nama_profile`),
  ADD UNIQUE KEY `profile_name` (`profile_name`),
  ADD KEY `idx_status` (`status_profile`),
  ADD KEY `idx_sync` (`sync_mikrotik`);

--
-- Indeks untuk tabel `hotspot_sales`
--
ALTER TABLE `hotspot_sales`
  ADD PRIMARY KEY (`id_sale`),
  ADD KEY `idx_user_hotspot` (`id_user_hotspot`),
  ADD KEY `idx_tanggal` (`tanggal_jual`),
  ADD KEY `idx_penjual` (`id_user_penjual`);

--
-- Indeks untuk tabel `hotspot_users`
--
ALTER TABLE `hotspot_users`
  ADD PRIMARY KEY (`id_user_hotspot`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_profile` (`id_profile`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_batch` (`batch_id`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_sync` (`sync_mikrotik`);

--
-- Indeks untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `idx_user` (`id_user`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_tabel` (`tabel_terkait`);

--
-- Indeks untuk tabel `mikrotik_hotspot_profiles`
--
ALTER TABLE `mikrotik_hotspot_profiles`
  ADD PRIMARY KEY (`id_profile`),
  ADD UNIQUE KEY `nama_profile` (`nama_profile`),
  ADD KEY `idx_status` (`status_profile`),
  ADD KEY `idx_sync` (`sync_mikrotik`),
  ADD KEY `idx_lock` (`lock_user_enabled`);

--
-- Indeks untuk tabel `monitoring_pppoe`
--
ALTER TABLE `monitoring_pppoe`
  ADD PRIMARY KEY (`id_monitoring`),
  ADD KEY `idx_pelanggan` (`id_pelanggan`),
  ADD KEY `idx_username` (`mikrotik_username`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_session_start` (`session_start`);

--
-- Indeks untuk tabel `paket_internet`
--
ALTER TABLE `paket_internet`
  ADD PRIMARY KEY (`id_paket`),
  ADD UNIQUE KEY `nama_paket` (`nama_paket`),
  ADD UNIQUE KEY `profile_name` (`profile_name`),
  ADD KEY `idx_status` (`status_paket`),
  ADD KEY `idx_sync` (`sync_mikrotik`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `idx_tagihan` (`id_tagihan`),
  ADD KEY `idx_pelanggan` (`id_pelanggan`),
  ADD KEY `idx_tanggal` (`tanggal_bayar`),
  ADD KEY `idx_user_pencatat` (`id_user_pencatat`);

--
-- Indeks untuk tabel `pengaturan_perusahaan`
--
ALTER TABLE `pengaturan_perusahaan`
  ADD PRIMARY KEY (`id_pengaturan`);

--
-- Indeks untuk tabel `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id_pengeluaran`);

--
-- Indeks untuk tabel `radcheck`
--
ALTER TABLE `radcheck`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tagihan`
--
ALTER TABLE `tagihan`
  ADD PRIMARY KEY (`id_tagihan`),
  ADD UNIQUE KEY `uk_pelanggan_bulan_tahun` (`id_pelanggan`,`bulan_tagihan`,`tahun_tagihan`),
  ADD KEY `idx_status` (`status_tagihan`),
  ADD KEY `idx_jatuh_tempo` (`tgl_jatuh_tempo`),
  ADD KEY `idx_auto_generate` (`id_pelanggan`,`tgl_jatuh_tempo`),
  ADD KEY `idx_bulan_tahun` (`bulan_tagihan`,`tahun_tagihan`),
  ADD KEY `fk_tagihan_generated_by` (`generated_by`);

--
-- Indeks untuk tabel `transaksi_lain`
--
ALTER TABLE `transaksi_lain`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tanggal` (`tanggal`),
  ADD KEY `idx_jenis` (`jenis`),
  ADD KEY `idx_kategori` (`kategori`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_status` (`status`);

--
-- Indeks untuk tabel `voucher_history`
--
ALTER TABLE `voucher_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_profile_name` (`profile_name`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_batch_id` (`batch_id`);

--
-- Indeks untuk tabel `voucher_temp`
--
ALTER TABLE `voucher_temp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_batch_id` (`batch_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `data_pelanggan`
--
ALTER TABLE `data_pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `ftth_odc`
--
ALTER TABLE `ftth_odc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `ftth_odc_ports`
--
ALTER TABLE `ftth_odc_ports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `ftth_odp`
--
ALTER TABLE `ftth_odp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `ftth_odp_ports`
--
ALTER TABLE `ftth_odp_ports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `ftth_olt`
--
ALTER TABLE `ftth_olt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `ftth_pon`
--
ALTER TABLE `ftth_pon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `ftth_pop`
--
ALTER TABLE `ftth_pop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `hotspot_profiles`
--
ALTER TABLE `hotspot_profiles`
  MODIFY `id_profile` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hotspot_sales`
--
ALTER TABLE `hotspot_sales`
  MODIFY `id_sale` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hotspot_users`
--
ALTER TABLE `hotspot_users`
  MODIFY `id_user_hotspot` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `mikrotik_hotspot_profiles`
--
ALTER TABLE `mikrotik_hotspot_profiles`
  MODIFY `id_profile` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `monitoring_pppoe`
--
ALTER TABLE `monitoring_pppoe`
  MODIFY `id_monitoring` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `paket_internet`
--
ALTER TABLE `paket_internet`
  MODIFY `id_paket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengaturan_perusahaan`
--
ALTER TABLE `pengaturan_perusahaan`
  MODIFY `id_pengaturan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id_pengeluaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `radcheck`
--
ALTER TABLE `radcheck`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `transaksi_lain`
--
ALTER TABLE `transaksi_lain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `voucher_history`
--
ALTER TABLE `voucher_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `voucher_temp`
--
ALTER TABLE `voucher_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `data_pelanggan`
--
ALTER TABLE `data_pelanggan`
  ADD CONSTRAINT `fk_pelanggan_odp` FOREIGN KEY (`odp_id`) REFERENCES `ftth_odp` (`id`),
  ADD CONSTRAINT `fk_pelanggan_odp_port` FOREIGN KEY (`odp_port_id`) REFERENCES `ftth_odp_ports` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pelanggan_paket` FOREIGN KEY (`id_paket`) REFERENCES `paket_internet` (`id_paket`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pelanggan_pop` FOREIGN KEY (`pop_id`) REFERENCES `ftth_pop` (`id`);

--
-- Ketidakleluasaan untuk tabel `ftth_odc`
--
ALTER TABLE `ftth_odc`
  ADD CONSTRAINT `fk_odc_pon` FOREIGN KEY (`pon_port_id`) REFERENCES `ftth_pon` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ftth_odc_ports`
--
ALTER TABLE `ftth_odc_ports`
  ADD CONSTRAINT `fk_odc_port_odc` FOREIGN KEY (`odc_id`) REFERENCES `ftth_odc` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ftth_odp`
--
ALTER TABLE `ftth_odp`
  ADD CONSTRAINT `fk_odp_odc_port` FOREIGN KEY (`odc_port_id`) REFERENCES `ftth_odc_ports` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ftth_odp_ports`
--
ALTER TABLE `ftth_odp_ports`
  ADD CONSTRAINT `fk_odp_port_customer` FOREIGN KEY (`connected_customer_id`) REFERENCES `data_pelanggan` (`id_pelanggan`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_odp_port_odp` FOREIGN KEY (`odp_id`) REFERENCES `ftth_odp` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ftth_olt`
--
ALTER TABLE `ftth_olt`
  ADD CONSTRAINT `fk_olt_pop` FOREIGN KEY (`pop_id`) REFERENCES `ftth_pop` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `ftth_pon`
--
ALTER TABLE `ftth_pon`
  ADD CONSTRAINT `fk_pon_olt` FOREIGN KEY (`olt_id`) REFERENCES `ftth_olt` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `hotspot_sales`
--
ALTER TABLE `hotspot_sales`
  ADD CONSTRAINT `fk_hotspot_sales_penjual` FOREIGN KEY (`id_user_penjual`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hotspot_sales_user` FOREIGN KEY (`id_user_hotspot`) REFERENCES `hotspot_users` (`id_user_hotspot`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `hotspot_users`
--
ALTER TABLE `hotspot_users`
  ADD CONSTRAINT `fk_hotspot_user_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hotspot_user_profile` FOREIGN KEY (`id_profile`) REFERENCES `hotspot_profiles` (`id_profile`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `monitoring_pppoe`
--
ALTER TABLE `monitoring_pppoe`
  ADD CONSTRAINT `fk_monitoring_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `data_pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pembayaran_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `data_pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pembayaran_tagihan` FOREIGN KEY (`id_tagihan`) REFERENCES `tagihan` (`id_tagihan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pembayaran_user` FOREIGN KEY (`id_user_pencatat`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tagihan`
--
ALTER TABLE `tagihan`
  ADD CONSTRAINT `fk_tagihan_generated_by` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tagihan_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `data_pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
