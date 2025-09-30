-- Database setup for LeanNext Maintenance System
-- This file contains the database structure for testing purposes

CREATE DATABASE IF NOT EXISTS leannext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE leannext;

-- Table for machines
CREATE TABLE IF NOT EXISTS mesin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_mesin VARCHAR(100) NOT NULL,
    kode_mesin VARCHAR(50) UNIQUE,
    lokasi VARCHAR(100),
    status ENUM('aktif', 'nonaktif', 'maintenance') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for maintenance records
CREATE TABLE IF NOT EXISTS perawatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mesin_id INT NOT NULL,
    tanggal DATETIME NOT NULL,
    keterangan TEXT,
    status ENUM('selesai', 'pending', 'dalam_proses') DEFAULT 'selesai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (mesin_id) REFERENCES mesin(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample machine data
INSERT INTO mesin (nama_mesin, kode_mesin, lokasi, status) VALUES
('Mesin A', 'MSN-A-001', 'Area Produksi 1', 'aktif'),
('Mesin B', 'MSN-B-002', 'Area Produksi 2', 'aktif'),
('Mesin C', 'MSN-C-003', 'Area Produksi 3', 'aktif');

-- Insert sample maintenance data
INSERT INTO perawatan (mesin_id, tanggal, keterangan, status) VALUES
(1, '2025-01-15 10:00:00', 'Perawatan rutin bulanan - pembersihan dan pelumasan', 'selesai'),
(2, '2025-01-16 14:30:00', 'Penggantian oli dan filter', 'selesai'),
(3, '2025-01-17 09:15:00', 'Kalibrasi sensor dan cek kondisi bearing', 'selesai');
