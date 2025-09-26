<?php
require_once '../config.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';
$message_type = '';

// Handle status update
if (isset($_POST['update_status'])) {
    $pengajuan_id = $_POST['pengajuan_id'];
    $new_status = $_POST['status'];
    $admin_notes = trim($_POST['admin_notes']);
    
    try {
        $stmt = $pdo->prepare("UPDATE pengajuan_barang SET status = ?, admin_notes = ?, processed_at = NOW() WHERE id = ?");
        $stmt->execute([$new_status, $admin_notes, $pengajuan_id]);
        
        $message = 'Status pengajuan berhasil diperbarui';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Terjadi kesalahan saat memperbarui status';
        $message_type = 'danger';
    }
}

// Get all pengajuan with user info
try {
    $stmt = $pdo->query("SELECT pb.*, u.username, u.full_name FROM pengajuan_barang pb LEFT JOIN users u ON pb.user_id = u.id ORDER BY pb.created_at DESC");
    $pengajuan_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pengajuan_list = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengajuan - LeanNext System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="p-4">
            <h4 class="text-white mb-0">
                <i class="fas fa-cog"></i> Admin Panel
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
                <a class="nav-link" href="users.php">
                    <i class="fas fa-users me-2"></i> Manajemen User
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="pengajuan_barang.php">
                    <i class="fas fa-box me-2"></i> Pengajuan Barang
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
            <h5 class="mb-0">Manajemen Pengajuan Barang</h5>
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

                <h5 class="mb-4">Daftar Pengajuan Barang</h5>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Pemohon</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Total Harga</th>
                                <th>Prioritas</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pengajuan_list)): ?>
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    Tidak ada pengajuan
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($pengajuan_list as $row): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['full_name'] ?? $row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><?= htmlspecialchars($row['kategori'] ?? '-') ?></td>
                                    <td><?= $row['jumlah'] ?></td>
                                    <td>Rp <?= number_format($row['jumlah'] * ($row['harga_satuan'] ?? 0), 0, ',', '.') ?></td>
                                    <td>
                                        <?php
                                        $priority_class = '';
                                        switch($row['prioritas']) {
                                            case 'urgent': $priority_class = 'bg-danger'; break;
                                            case 'tinggi': $priority_class = 'bg-warning text-dark'; break;
                                            case 'sedang': $priority_class = 'bg-info'; break;
                                            case 'rendah': $priority_class = 'bg-secondary'; break;
                                            default: $priority_class = 'bg-secondary';
                                        }
                                        ?>
                                        <span class="badge <?= $priority_class ?>"><?= ucfirst($row['prioritas']) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = '';
                                        switch($row['status']) {
                                            case 'pending': $badge_class = 'bg-warning text-dark'; break;
                                            case 'approved': $badge_class = 'bg-success'; break;
                                            case 'rejected': $badge_class = 'bg-danger'; break;
                                            default: $badge_class = 'bg-secondary';
                                        }
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= ucfirst($row['status']) ?></span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" data-bs-target="#modal<?= $row['id'] ?>">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal for each pengajuan -->
                                <div class="modal fade" id="modal<?= $row['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail Pengajuan #<?= $row['id'] ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>Pemohon:</strong> <?= htmlspecialchars($row['full_name'] ?? $row['username']) ?></p>
                                                        <p><strong>Nama Barang:</strong> <?= htmlspecialchars($row['nama_barang']) ?></p>
                                                        <p><strong>Kategori:</strong> <?= htmlspecialchars($row['kategori'] ?? '-') ?></p>
                                                        <p><strong>Jumlah:</strong> <?= $row['jumlah'] ?> pcs</p>
                                                        <p><strong>Harga Satuan:</strong> Rp <?= number_format($row['harga_satuan'] ?? 0, 0, ',', '.') ?></p>
                                                        <p><strong>Total Harga:</strong> Rp <?= number_format($row['jumlah'] * ($row['harga_satuan'] ?? 0), 0, ',', '.') ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Prioritas:</strong> <span class="badge <?= $priority_class ?>"><?= ucfirst($row['prioritas']) ?></span></p>
                                                        <p><strong>Status:</strong> <span class="badge <?= $badge_class ?>"><?= ucfirst($row['status']) ?></span></p>
                                                        <p><strong>Tanggal Pengajuan:</strong> <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></p>
                                                        <?php if ($row['processed_at']): ?>
                                                        <p><strong>Tanggal Diproses:</strong> <?= date('d/m/Y H:i', strtotime($row['processed_at'])) ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <?php if ($row['keterangan']): ?>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <p><strong>Keterangan:</strong></p>
                                                        <p class="bg-light p-3 rounded"><?= nl2br(htmlspecialchars($row['keterangan'])) ?></p>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <form method="POST">
                                                    <input type="hidden" name="pengajuan_id" value="<?= $row['id'] ?>">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Update Status:</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                                <option value="approved" <?= $row['status'] == 'approved' ? 'selected' : '' ?>>Disetujui</option>
                                                                <option value="rejected" <?= $row['status'] == 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Catatan Admin:</label>
                                                            <textarea name="admin_notes" class="form-control" rows="3" placeholder="Berikan catatan atau alasan..."><?= htmlspecialchars($row['admin_notes'] ?? '') ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <button type="submit" name="update_status" class="btn btn-primary">
                                                            <i class="fas fa-save"></i> Update Status
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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