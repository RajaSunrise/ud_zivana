-- Database structure for ud_zivana2

CREATE DATABASE IF NOT EXISTS `ud_zivana2`;
USE `ud_zivana2`;

-- Table structure for `users`
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `role` enum('admin','customer') NOT NULL DEFAULT 'customer',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `produk`
CREATE TABLE IF NOT EXISTS `produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(255) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `harga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `pesanan`
CREATE TABLE IF NOT EXISTS `pesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `status_pembayaran` varchar(50) NOT NULL DEFAULT 'pending',
  `status_pesanan` varchar(50) NOT NULL DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `tanggal_pesan` timestamp NOT NULL DEFAULT current_timestamp(),
  `bukti_transfer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pesanan_user` (`user_id`),
  CONSTRAINT `fk_pesanan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `detail_pesanan`
CREATE TABLE IF NOT EXISTS `detail_pesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pesanan_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_detail_pesanan` (`pesanan_id`),
  KEY `fk_detail_produk` (`produk_id`),
  CONSTRAINT `fk_detail_pesanan` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_produk` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `hutang`
CREATE TABLE IF NOT EXISTS `hutang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pesanan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_hutang` decimal(15,2) NOT NULL,
  `sisa_hutang` decimal(15,2) NOT NULL,
  `jatuh_tempo` date DEFAULT NULL,
  `status` enum('lunas','belum_lunas') NOT NULL DEFAULT 'belum_lunas',
  PRIMARY KEY (`id`),
  KEY `fk_hutang_pesanan` (`pesanan_id`),
  KEY `fk_hutang_user` (`user_id`),
  CONSTRAINT `fk_hutang_pesanan` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_hutang_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `pembayaran_hutang`
CREATE TABLE IF NOT EXISTS `pembayaran_hutang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hutang_id` int(11) NOT NULL,
  `jumlah_bayar` decimal(15,2) NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `dikonfirmasi_admin` tinyint(1) NOT NULL DEFAULT 0,
  `tanggal_bayar` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_pembayaran_hutang` (`hutang_id`),
  CONSTRAINT `fk_pembayaran_hutang` FOREIGN KEY (`hutang_id`) REFERENCES `hutang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user
INSERT INTO `users` (`nama`, `email`, `password`, `no_hp`, `alamat`, `role`) VALUES
('Admin', 'admin@example.com', 'admin', '08123456789', 'Jl. Admin', 'admin');
