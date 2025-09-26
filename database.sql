-- Database: leannext
-- Create database if not exists
CREATE DATABASE IF NOT EXISTS leannext;
USE leannext;

-- Table structure for users
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'kaprog') NOT NULL,
    full_name VARCHAR(100),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table structure for pengajuan_barang
CREATE TABLE IF NOT EXISTS pengajuan_barang (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    nama_barang VARCHAR(255) NOT NULL,
    kategori VARCHAR(100),
    jumlah INT NOT NULL DEFAULT 1,
    harga_satuan DECIMAL(15,2) DEFAULT 0.00,
    keterangan TEXT,
    prioritas ENUM('rendah', 'sedang', 'tinggi', 'urgent') DEFAULT 'sedang',
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, role, full_name, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator', 'admin@leannext.com')
ON DUPLICATE KEY UPDATE username = username;

-- Insert sample kaprog user (password: kaprog123)
INSERT INTO users (username, password, role, full_name, email) VALUES 
('kaprog', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'kaprog', 'Kepala Program', 'kaprog@leannext.com')
ON DUPLICATE KEY UPDATE username = username;

-- Insert sample pengajuan data
INSERT INTO pengajuan_barang (user_id, nama_barang, kategori, jumlah, harga_satuan, keterangan, prioritas, status) VALUES
((SELECT id FROM users WHERE username = 'kaprog'), 'Laptop Dell Inspiron', 'Elektronik', 2, 8000000.00, 'Untuk keperluan pembelajaran programming', 'tinggi', 'pending'),
((SELECT id FROM users WHERE username = 'kaprog'), 'Whiteboard', 'Furniture', 3, 500000.00, 'Untuk ruang kelas baru', 'sedang', 'approved'),
((SELECT id FROM users WHERE username = 'kaprog'), 'Spidol Boardmarker', 'Alat Tulis', 50, 15000.00, 'Spidol untuk whiteboard', 'rendah', 'pending')
ON DUPLICATE KEY UPDATE nama_barang = nama_barang;