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

// Get pengajuan ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: pengajuan_barang.php');
    exit();
}

$pengajuan_id = $_GET['id'];

// Get pengajuan data
try {
    $stmt = $pdo->prepare("SELECT * FROM pengajuan_barang WHERE id = ? AND user_id = ?");
    $stmt->execute([$pengajuan_id, $user_id]);
    $pengajuan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pengajuan) {
        header('Location: pengajuan_barang.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: pengajuan_barang.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Only allow editing if status is pending
    if ($pengajuan['status'] !== 'pending') {
        $message = 'Pengajuan yang sudah diproses tidak dapat diubah';
        $message_type = 'danger';
    } else {
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
                $stmt = $pdo->prepare("UPDATE pengajuan_barang SET nama_barang = ?, kategori = ?, jumlah = ?, harga_satuan = ?, keterangan = ?, prioritas = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
                $stmt->execute([$nama_barang, $kategori, $jumlah, $harga_satuan, $keterangan, $prioritas, $pengajuan_id, $user_id]);
                
                $message = 'Pengajuan barang berhasil diperbarui';
                $message_type = 'success';
                
                // Refresh data
                $stmt = $pdo->prepare("SELECT * FROM pengajuan_barang WHERE id = ? AND user_id = ?");
                $stmt->execute([$pengajuan_id, $user_id]);
                $pengajuan = $stmt->fetch(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                $message = 'Terjadi kesalahan saat memperbarui data';
                $message_type = 'danger';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengajuan - LeanNext System</title>
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
        .readonly-field {
            background-color: #f8f9fa !important;
            cursor: not-allowed;
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
                <a class="nav-link" href="tambah_pengajuan.php">
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
            <h5 class="mb-0">Edit Pengajuan Barang</h5>
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
                    <h5 class="mb-0">Edit Pengajuan #<?= $pengajuan['id'] ?></h5>
                    <div>
                        <span class="badge bg-<?= $pengajuan['status'] == 'pending' ? 'warning text-dark' : ($pengajuan['status'] == 'approved' ? 'success' : 'danger') ?> me-2">
                            <?= ucfirst($pengajuan['status']) ?>
                        </span>
                        <a href="pengajuan_barang.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <?php if ($pengajuan['status'] !== 'pending'): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    Pengajuan ini sudah diproses dan tidak dapat diubah lagi. Status: <strong><?= ucfirst($pengajuan['status']) ?></strong>
                    <?php if (!empty($pengajuan['admin_notes'])): ?>
                    <br><strong>Catatan Admin:</strong> <?= htmlspecialchars($pengajuan['admin_notes']) ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= $pengajuan['status'] !== 'pending' ? 'readonly-field' : '' ?>" 
                                       id="nama_barang" name="nama_barang" 
                                       value="<?= htmlspecialchars($pengajuan['nama_barang']) ?>" 
                                       <?= $pengajuan['status'] !== 'pending' ? 'readonly' : 'required' ?>>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select <?= $pengajuan['status'] !== 'pending' ? 'readonly-field' : '' ?>" 
                                        id="kategori" name="kategori" 
                                        <?= $pengajuan['status'] !== 'pending' ? 'disabled' : 'required' ?>>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Alat Tulis" <?= $pengajuan['kategori'] == 'Alat Tulis' ? 'selected' : '' ?>>Alat Tulis</option>
                                    <option value="Elektronik" <?= $pengajuan['kategori'] == 'Elektronik' ? 'selected' : '' ?>>Elektronik</option>
                                    <option value="Furniture" <?= $pengajuan['kategori'] == 'Furniture' ? 'selected' : '' ?>>Furniture</option>
                                    <option value="Peralatan Lab" <?= $pengajuan['kategori'] == 'Peralatan Lab' ? 'selected' : '' ?>>Peralatan Lab</option>
                                    <option value="Peralatan Olahraga" <?= $pengajuan['kategori'] == 'Peralatan Olahraga' ? 'selected' : '' ?>>Peralatan Olahraga</option>
                                    <option value="Lainnya" <?= $pengajuan['kategori'] == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control <?= $pengajuan['status'] !== 'pending' ? 'readonly-field' : '' ?>" 
                                           id="jumlah" name="jumlah" 
                                           value="<?= $pengajuan['jumlah'] ?>" min="1" 
                                           <?= $pengajuan['status'] !== 'pending' ? 'readonly' : 'required' ?>>
                                    <span class="input-group-text">pcs</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="harga_satuan" class="form-label">Harga Satuan (Rp)</label>
                                <input type="number" class="form-control <?= $pengajuan['status'] !== 'pending' ? 'readonly-field' : '' ?>" 
                                       id="harga_satuan" name="harga_satuan" 
                                       value="<?= $pengajuan['harga_satuan'] ?>" min="0" step="1000"
                                       <?= $pengajuan['status'] !== 'pending' ? 'readonly' : '' ?>>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="prioritas" class="form-label">Prioritas</label>
                                <select class="form-select <?= $pengajuan['status'] !== 'pending' ? 'readonly-field' : '' ?>" 
                                        id="prioritas" name="prioritas"
                                        <?= $pengajuan['status'] !== 'pending' ? 'disabled' : '' ?>>
                                    <option value="rendah" <?= $pengajuan['prioritas'] == 'rendah' ? 'selected' : '' ?>>Rendah</option>
                                    <option value="sedang" <?= $pengajuan['prioritas'] == 'sedang' ? 'selected' : '' ?>>Sedang</option>
                                    <option value="tinggi" <?= $pengajuan['prioritas'] == 'tinggi' ? 'selected' : '' ?>>Tinggi</option>
                                    <option value="urgent" <?= $pengajuan['prioritas'] == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control <?= $pengajuan['status'] !== 'pending' ? 'readonly-field' : '' ?>" 
                                  id="keterangan" name="keterangan" rows="4" 
                                  <?= $pengajuan['status'] !== 'pending' ? 'readonly' : '' ?>
                                  placeholder="Jelaskan detail kebutuhan, spesifikasi, atau alasan pengajuan..."><?= htmlspecialchars($pengajuan['keterangan']) ?></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Detail Pengajuan:</h6>
                                    <ul class="mb-0">
                                        <li><strong>Dibuat:</strong> <?= date('d/m/Y H:i', strtotime($pengajuan['created_at'])) ?></li>
                                        <?php if ($pengajuan['updated_at']): ?>
                                        <li><strong>Diperbarui:</strong> <?= date('d/m/Y H:i', strtotime($pengajuan['updated_at'])) ?></li>
                                        <?php endif; ?>
                                        <li><strong>Status:</strong> <?= ucfirst($pengajuan['status']) ?></li>
                                        <li><strong>Total Harga:</strong> Rp <?= number_format($pengajuan['jumlah'] * ($pengajuan['harga_satuan'] ?? 0), 0, ',', '.') ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($pengajuan['status'] !== 'pending'): ?>
                        <div class="col-md-6">
                            <div class="card border-<?= $pengajuan['status'] == 'approved' ? 'success' : 'danger' ?>">
                                <div class="card-body">
                                    <h6 class="card-title text-<?= $pengajuan['status'] == 'approved' ? 'success' : 'danger' ?>">
                                        Status: <?= ucfirst($pengajuan['status']) ?>
                                    </h6>
                                    <?php if (!empty($pengajuan['admin_notes'])): ?>
                                    <p class="mb-0"><strong>Catatan Admin:</strong><br><?= nl2br(htmlspecialchars($pengajuan['admin_notes'])) ?></p>
                                    <?php endif; ?>
                                    <?php if ($pengajuan['processed_at']): ?>
                                    <small class="text-muted">Diproses pada: <?= date('d/m/Y H:i', strtotime($pengajuan['processed_at'])) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2">
                        <?php if ($pengajuan['status'] === 'pending'): ?>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <?php endif; ?>
                        <a href="pengajuan_barang.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
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
</body>
</html>