<?php
// Database connection
$host = 'localhost';
$dbname = 'leannext';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Database connection for demo purposes - in production this would be in a config file
}

// Handle AJAX request for adding maintenance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_perawatan') {
    // Clean output buffer to ensure clean JSON response
    ob_clean();
    header('Content-Type: application/json');
    
    try {
        $mesin_id = $_POST['mesin_id'] ?? null;
        $keterangan = $_POST['keterangan'] ?? '';
        $tanggal = date('Y-m-d H:i:s');
        
        if (empty($mesin_id) || empty($keterangan)) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit;
        }
        
        // Insert maintenance data
        $stmt = $pdo->prepare("INSERT INTO perawatan (mesin_id, tanggal, keterangan, status) VALUES (?, ?, ?, 'selesai')");
        $stmt->execute([$mesin_id, $tanggal, $keterangan]);
        
        echo json_encode(['success' => true, 'message' => 'Perawatan berhasil disimpan']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Get maintenance data
$maintenance_data = [];
try {
    $stmt = $pdo->query("SELECT p.*, m.nama_mesin FROM perawatan p LEFT JOIN mesin m ON p.mesin_id = m.id ORDER BY p.tanggal DESC LIMIT 50");
    $maintenance_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Handle error silently in demo
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Perawatan - LeanNext</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data Perawatan Rutin</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#perawatanModal">
                            <i class="bi bi-plus-circle"></i> Lakukan Perawatan Sekarang
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="alertContainer"></div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Mesin</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="maintenanceTableBody">
                                    <?php foreach ($maintenance_data as $index => $data): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($data['nama_mesin'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($data['tanggal'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($data['keterangan'] ?? ''); ?></td>
                                        <td><span class="badge bg-success"><?php echo htmlspecialchars($data['status'] ?? ''); ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Perawatan - FIXED: data-bs-dismiss="modal" instead of "alert" -->
    <div class="modal fade" id="perawatanModal" tabindex="-1" aria-labelledby="perawatanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="perawatanModalLabel">Lakukan Perawatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="perawatanForm">
                        <div class="mb-3">
                            <label for="mesinSelect" class="form-label">Pilih Mesin</label>
                            <select class="form-select" id="mesinSelect" name="mesin_id" required>
                                <option value="">-- Pilih Mesin --</option>
                                <option value="1">Mesin A</option>
                                <option value="2">Mesin B</option>
                                <option value="3">Mesin C</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="keteranganTextarea" class="form-label">Keterangan Perawatan</label>
                            <textarea class="form-control" id="keteranganTextarea" name="keterangan" rows="4" required placeholder="Masukkan detail perawatan yang dilakukan..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="submitPerawatan">Simpan Perawatan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Perawatan - FIXED: data-bs-dismiss="modal" instead of "alert" -->
    <div class="modal fade" id="editPerawatanModal" tabindex="-1" aria-labelledby="editPerawatanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editPerawatanModalLabel">Edit Perawatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editPerawatanForm">
                        <input type="hidden" id="editPerawatanId" name="id">
                        <div class="mb-3">
                            <label for="editMesinSelect" class="form-label">Pilih Mesin</label>
                            <select class="form-select" id="editMesinSelect" name="mesin_id" required>
                                <option value="">-- Pilih Mesin --</option>
                                <option value="1">Mesin A</option>
                                <option value="2">Mesin B</option>
                                <option value="3">Mesin C</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editKeteranganTextarea" class="form-label">Keterangan Perawatan</label>
                            <textarea class="form-control" id="editKeteranganTextarea" name="keterangan" rows="4" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning" id="updatePerawatan">Update Perawatan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // FIXED: Removed duplicate button elements and cleaned up JavaScript structure
        
        // Show alert function - FIXED: Clean HTML structure without duplications
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            const alertHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            alertContainer.innerHTML = alertHTML;
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                const alertElement = alertContainer.querySelector('.alert');
                if (alertElement) {
                    const bsAlert = new bootstrap.Alert(alertElement);
                    bsAlert.close();
                }
            }, 5000);
        }
        
        // Submit maintenance form
        document.getElementById('submitPerawatan').addEventListener('click', function() {
            const form = document.getElementById('perawatanForm');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const formData = new FormData(form);
            formData.append('action', 'add_perawatan');
            
            // Disable button during submission
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            
            // FIXED: Proper AJAX handler with clean JSON response handling
            fetch('data_perawatan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Response is not JSON');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('perawatanModal'));
                    modal.hide();
                    
                    // Reset form
                    form.reset();
                    
                    // FIXED: Enhanced auto-refresh implementation
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert(data.message || 'Terjadi kesalahan', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan saat menyimpan data: ' + error.message, 'danger');
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
                this.innerHTML = 'Simpan Perawatan';
            });
        });
        
        // Reset form when modal is hidden
        document.getElementById('perawatanModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('perawatanForm').reset();
        });
        
        // Console log for debugging
        console.log('Data Perawatan page loaded successfully');
    </script>
</body>
</html>
