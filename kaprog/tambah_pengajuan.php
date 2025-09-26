<?php
require_once '../config.php';

// Check if user is logged in and has kaprog role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kaprog') {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = trim($_POST['nama_barang']);
    $kategori = trim($_POST['kategori']);
    $jumlah = (int)$_POST['jumlah'];
    $harga_satuan = (float)$_POST['harga_satuan'];
    $keterangan = trim($_POST['keterangan']);
    $prioritas = $_POST['prioritas'];
    
    // Validation
    if (empty($nama_barang) || empty($kategori) || $jumlah <= 0 || $harga_satuan < 0) {
        $message = 'Semua field wajib diisi dengan benar';
        $message_type = 'danger';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO pengajuan_barang (user_id, nama_barang, kategori, jumlah, harga_satuan, keterangan, prioritas, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->execute([$user_id, $nama_barang, $kategori, $jumlah, $harga_satuan, $keterangan, $prioritas]);
            
            $message = 'Pengajuan barang berhasil ditambahkan';
            $message_type = 'success';
            
            // Reset form
            $_POST = [];
        } catch (PDOException $e) {
            $message = 'Terjadi kesalahan saat menyimpan data';
            $message_type = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengajuan - LeanNext System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 10px;
            margin: 2px 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }
        .topbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px 30px;
        }
        .content-wrapper {
            padding: 30px;
            background: #f8f9fa;
            min-height: calc(100vh - 80px);
        }
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .footer {
            background: white;
            padding: 20px 30px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="p-4">
            <h4 class="text-white mb-0">
                <i class="fas fa-user-tie"></i> Kaprog Panel
            </h4>
            <small class="text-white-50">LeanNext System</small>
        </div>
        
        <ul class="nav nav-pills flex-column px-3">
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pengajuan_barang.php">
                    <i class="fas fa-box me-2"></i> Pengajuan Barang
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="tambah_pengajuan.php">
                    <i class="fas fa-plus-circle me-2"></i> Tambah Pengajuan
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link text-warning" href="logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah Pengajuan Barang</h5>
            <div class="d-flex align-items-center">
                <span class="me-3">Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <img src="https://via.placeholder.com/40x40" class="rounded-circle" alt="Profile">
            </div>
        </header>

        <!-- Content -->
        <div class="content-wrapper">
            <div class="content-card">
                <?php if ($message): ?>
                <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Form Pengajuan Barang Baru</h5>
                    <a href="pengajuan_barang.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_barang" name="nama_barang" 
                                       value="<?= htmlspecialchars($_POST['nama_barang'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select" id="kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Alat Tulis" <?= ($_POST['kategori'] ?? '') == 'Alat Tulis' ? 'selected' : '' ?>>Alat Tulis</option>
                                    <option value="Elektronik" <?= ($_POST['kategori'] ?? '') == 'Elektronik' ? 'selected' : '' ?>>Elektronik</option>
                                    <option value="Furniture" <?= ($_POST['kategori'] ?? '') == 'Furniture' ? 'selected' : '' ?>>Furniture</option>
                                    <option value="Peralatan Lab" <?= ($_POST['kategori'] ?? '') == 'Peralatan Lab' ? 'selected' : '' ?>>Peralatan Lab</option>
                                    <option value="Peralatan Olahraga" <?= ($_POST['kategori'] ?? '') == 'Peralatan Olahraga' ? 'selected' : '' ?>>Peralatan Olahraga</option>
                                    <option value="Lainnya" <?= ($_POST['kategori'] ?? '') == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="jumlah" name="jumlah" 
                                           value="<?= htmlspecialchars($_POST['jumlah'] ?? '') ?>" min="1" required>
                                    <span class="input-group-text">pcs</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="harga_satuan" class="form-label">Harga Satuan (Rp)</label>
                                <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" 
                                       value="<?= htmlspecialchars($_POST['harga_satuan'] ?? '') ?>" min="0" step="1000">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="prioritas" class="form-label">Prioritas</label>
                                <select class="form-select" id="prioritas" name="prioritas">
                                    <option value="rendah" <?= ($_POST['prioritas'] ?? '') == 'rendah' ? 'selected' : '' ?>>Rendah</option>
                                    <option value="sedang" <?= ($_POST['prioritas'] ?? 'sedang') == 'sedang' ? 'selected' : '' ?>>Sedang</option>
                                    <option value="tinggi" <?= ($_POST['prioritas'] ?? '') == 'tinggi' ? 'selected' : '' ?>>Tinggi</option>
                                    <option value="urgent" <?= ($_POST['prioritas'] ?? '') == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="4" 
                                  placeholder="Jelaskan detail kebutuhan, spesifikasi, atau alasan pengajuan..."><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Informasi:</h6>
                                <ul class="mb-0">
                                    <li>Status pengajuan akan "Pending" setelah disubmit</li>
                                    <li>Admin akan memproses pengajuan Anda</li>
                                    <li>Anda akan menerima notifikasi hasil persetujuan</li>
                                    <li>Pengajuan yang sudah disetujui/ditolak tidak dapat diubah</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Pengajuan
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i> Reset Form
                        </button>
                        <a href="pengajuan_barang.php" class="btn btn-outline-danger">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p class="mb-0">&copy; 2024 LeanNext System. All rights reserved.</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto calculate total price
        document.getElementById('jumlah').addEventListener('input', calculateTotal);
        document.getElementById('harga_satuan').addEventListener('input', calculateTotal);
        
        function calculateTotal() {
            const jumlah = parseInt(document.getElementById('jumlah').value) || 0;
            const harga = parseFloat(document.getElementById('harga_satuan').value) || 0;
            const total = jumlah * harga;
            
            // You can add a display element for total if needed
            console.log('Total: Rp ' + total.toLocaleString('id-ID'));
        }
    </script>
</body>
</html>