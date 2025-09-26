<?php
require_once '../config.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get dashboard statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users WHERE role != 'admin'");
    $total_users = $stmt->fetch()['total_users'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total_pengajuan FROM pengajuan_barang");
    $total_pengajuan = $stmt->fetch()['total_pengajuan'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as pending_pengajuan FROM pengajuan_barang WHERE status = 'pending'");
    $pending_pengajuan = $stmt->fetch()['pending_pengajuan'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as approved_pengajuan FROM pengajuan_barang WHERE status = 'approved'");
    $approved_pengajuan = $stmt->fetch()['approved_pengajuan'];
} catch (PDOException $e) {
    $total_users = 0;
    $total_pengajuan = 0;
    $pending_pengajuan = 0;
    $approved_pengajuan = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - LeanNext System</title>
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
                <i class="fas fa-cog"></i> Admin Panel
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
                <a class="nav-link" href="users.php">
                    <i class="fas fa-users me-2"></i> Manajemen User
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pengajuan_barang.php">
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
            <h5 class="mb-0">Dashboard Admin</h5>
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
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?= $total_users ?></h3>
                                <p class="text-muted mb-0">Total Users</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success text-white me-3">
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
                            <div class="stat-icon bg-info text-white me-3">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h3 class="mb-0"><?= $approved_pengajuan ?></h3>
                                <p class="text-muted mb-0">Approved</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="stat-card">
                        <h5 class="mb-4">Pengajuan Barang Terbaru</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Barang</th>
                                        <th>Pemohon</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try {
                                        $stmt = $pdo->query("SELECT pb.*, u.username FROM pengajuan_barang pb LEFT JOIN users u ON pb.user_id = u.id ORDER BY pb.created_at DESC LIMIT 5");
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                        <td><?= htmlspecialchars($row['username'] ?? 'Unknown') ?></td>
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
                                            <a href="pengajuan_barang.php?view=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
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