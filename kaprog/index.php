<?php
require_once '../config.php';

// Check if user is logged in and has kaprog role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kaprog') {
    header('Location: ../login.php');
    exit();
}

// Get dashboard statistics for kaprog
try {
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_pengajuan FROM pengajuan_barang WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_pengajuan = $stmt->fetch()['total_pengajuan'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as pending_pengajuan FROM pengajuan_barang WHERE user_id = ? AND status = 'pending'");
    $stmt->execute([$user_id]);
    $pending_pengajuan = $stmt->fetch()['pending_pengajuan'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as approved_pengajuan FROM pengajuan_barang WHERE user_id = ? AND status = 'approved'");
    $stmt->execute([$user_id]);
    $approved_pengajuan = $stmt->fetch()['approved_pengajuan'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as rejected_pengajuan FROM pengajuan_barang WHERE user_id = ? AND status = 'rejected'");
    $stmt->execute([$user_id]);
    $rejected_pengajuan = $stmt->fetch()['rejected_pengajuan'];
} catch (PDOException $e) {
    $total_pengajuan = 0;
    $pending_pengajuan = 0;
    $approved_pengajuan = 0;
    $rejected_pengajuan = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kaprog - LeanNext System</title>
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
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
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
                <i class="fas fa-user-tie"></i> Kaprog Panel
            </h4>
            <small class="text-white-50">LeanNext System</small>
        </div>
        
        <ul class="nav nav-pills flex-column px-3">
            <li class="nav-item">
                <a class="nav-link active" href="index.php">
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
            <h5 class="mb-0">Dashboard Kaprog</h5>
            <div class="d-flex align-items-center">
                <span class="me-3">Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <img src="https://via.placeholder.com/40x40" class="rounded-circle" alt="Profile">
            </div>
        </header>

        <!-- Content -->
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-primary text-white me-3">
                                <i class="fas fa-box"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?= $total_pengajuan ?></h3>
                                <p class="text-muted mb-0">Total Pengajuan</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-warning text-white me-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?= $pending_pengajuan ?></h3>
                                <p class="text-muted mb-0">Pending</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success text-white me-3">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?= $approved_pengajuan ?></h3>
                                <p class="text-muted mb-0">Disetujui</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-danger text-white me-3">
                                <i class="fas fa-times"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?= $rejected_pengajuan ?></h3>
                                <p class="text-muted mb-0">Ditolak</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">Pengajuan Barang Saya</h5>
                            <a href="tambah_pengajuan.php" class="btn btn-success">
                                <i class="fas fa-plus"></i> Tambah Pengajuan
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $stmt = $pdo->prepare("SELECT * FROM pengajuan_barang WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
                                        $stmt->execute([$user_id]);
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                        <td><?= $row['jumlah'] ?></td>
                                        <td>
                                            <?php
                                            $badge_class = '';
                                            switch($row['status']) {
                                                case 'pending': $badge_class = 'bg-warning'; break;
                                                case 'approved': $badge_class = 'bg-success'; break;
                                                case 'rejected': $badge_class = 'bg-danger'; break;
                                                default: $badge_class = 'bg-secondary';
                                            }
                                            ?>
                                            <span class="badge <?= $badge_class ?>"><?= ucfirst($row['status']) ?></span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                        <td>
                                            <a href="edit_pengajuan.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($row['status'] == 'pending'): ?>
                                            <a href="pengajuan_barang.php?delete=<?= $row['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php 
                                        endwhile;
                                    } catch (PDOException $e) {
                                        echo '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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