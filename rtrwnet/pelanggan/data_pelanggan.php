<?php
ob_start(); // Start output buffering at the VERY TOP

// /pelanggan/data_pelanggan.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load konfigurasi
require_once __DIR__ . '/../config/config_database.php';
require_once __DIR__ . '/../config/config_mikrotik.php';
require_once __DIR__ . '/../config/routeros_api.php';
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/sidebar.php';

// Initialize CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';
$pelanggan_list = [];
$total_pelanggan = 0;

// Handle success/error messages from redirect
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Handle pencarian dan filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$paket_filter = isset($_GET['paket']) ? $_GET['paket'] : '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Handle aksi (activate, deactivate, isolir) - HAPUS bagian delete dari sini
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id_pelanggan = isset($_POST['id_pelanggan']) ? (int)$_POST['id_pelanggan'] : 0;
    
    if ($id_pelanggan > 0) {
        try {
            switch ($action) {
                case 'activate':
                    $update_query = "UPDATE data_pelanggan SET status_aktif = 'aktif' WHERE id_pelanggan = ?";
                    $stmt = $mysqli->prepare($update_query);
                    $stmt->bind_param("i", $id_pelanggan);
                    $stmt->execute();
                    $success = "Pelanggan berhasil diaktifkan!";
                    break;
                    
                case 'deactivate':
                    $update_query = "UPDATE data_pelanggan SET status_aktif = 'nonaktif' WHERE id_pelanggan = ?";
                    $stmt = $mysqli->prepare($update_query);
                    $stmt->bind_param("i", $id_pelanggan);
                    $stmt->execute();
                    $success = "Pelanggan berhasil dinonaktifkan!";
                    break;
                    
                case 'isolir':
                    $update_query = "UPDATE data_pelanggan SET status_aktif = 'isolir' WHERE id_pelanggan = ?";
                    $stmt = $mysqli->prepare($update_query);
                    $stmt->bind_param("i", $id_pelanggan);
                    $stmt->execute();
                    $success = "Pelanggan berhasil diisolir!";
                    break;
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Query untuk mengambil data pelanggan dengan filter dan pencarian
try {
    // Base query
    $where_conditions = [];
    $params = [];
    $param_types = '';
    
    // Kondisi pencarian
    if (!empty($search)) {
        $where_conditions[] = "(dp.nama_pelanggan LIKE ? OR dp.alamat_pelanggan LIKE ? OR dp.telepon_pelanggan LIKE ? OR dp.mikrotik_username LIKE ?)";
        $search_term = "%{$search}%";
        $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
        $param_types .= 'ssss';
    }
    
    // Filter status
    if (!empty($status_filter)) {
        $where_conditions[] = "dp.status_aktif = ?";
        $params[] = $status_filter;
        $param_types .= 's';
    }
    
    // Filter paket
    if (!empty($paket_filter)) {
        $where_conditions[] = "dp.id_paket = ?";
        $params[] = $paket_filter;
        $param_types .= 'i';
    }
    
    // Gabungkan kondisi WHERE
    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }
    
    // Query untuk total data (untuk pagination)
    $count_query = "SELECT COUNT(*) as total 
                    FROM data_pelanggan dp 
                    LEFT JOIN paket_internet pi ON dp.id_paket = pi.id_paket 
                    {$where_clause}";
    
    if (!empty($params)) {
        $count_stmt = $mysqli->prepare($count_query);
        $count_stmt->bind_param($param_types, ...$params);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
    } else {
        $count_result = $mysqli->query($count_query);
    }
    
    $total_pelanggan = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_pelanggan / $limit);
    
    // Query untuk data pelanggan dengan informasi tambahan untuk delete
    $main_query = "SELECT 
                    dp.id_pelanggan,
                    dp.nama_pelanggan,
                    dp.alamat_pelanggan,
                    dp.telepon_pelanggan,
                    dp.email_pelanggan,
                    dp.tgl_daftar,
                    dp.tgl_expired,
                    dp.status_aktif,
                    dp.mikrotik_username,
                    dp.mikrotik_password,
                    dp.mikrotik_profile,
                    dp.last_paid_date,
                    pi.nama_paket,
                    pi.harga,
                    pi.rate_limit_rx,
                    pi.rate_limit_tx,
                    (SELECT COUNT(*) FROM tagihan t WHERE t.id_pelanggan = dp.id_pelanggan AND t.status_tagihan IN ('belum_bayar', 'terlambat')) as unpaid_bills,
                    (SELECT COUNT(*) FROM pembayaran p WHERE p.id_pelanggan = dp.id_pelanggan) as payment_count,
                    (SELECT COUNT(*) FROM monitoring_pppoe mp WHERE mp.id_pelanggan = dp.id_pelanggan AND mp.status = 'active') as active_connections
                    FROM data_pelanggan dp
                    LEFT JOIN paket_internet pi ON dp.id_paket = pi.id_paket
                    {$where_clause}
                    ORDER BY dp.created_at DESC
                    LIMIT {$limit} OFFSET {$offset}";
    
    if (!empty($params)) {
        $main_stmt = $mysqli->prepare($main_query);
        $main_stmt->bind_param($param_types, ...$params);
        $main_stmt->execute();
        $result = $main_stmt->get_result();
    } else {
        $result = $mysqli->query($main_query);
    }
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $pelanggan_list[] = $row;
        }
    }
    
    // Ambil daftar paket untuk filter
    $paket_query = "SELECT id_paket, nama_paket FROM paket_internet WHERE status_paket = 'aktif' ORDER BY nama_paket";
    $paket_result = $mysqli->query($paket_query);
    $paket_options = [];
    while ($row = $paket_result->fetch_assoc()) {
        $paket_options[] = $row;
    }
    
} catch (Exception $e) {
    $error = "Gagal mengambil data pelanggan: " . $e->getMessage();
}

// Function untuk format rupiah
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Function untuk format tanggal
function format_date($date) {
    return date('d/m/Y', strtotime($date));
}

// Function untuk mendapatkan badge status
function getStatusBadge($status) {
    $badges = [
        'aktif' => '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Aktif</span>',
        'nonaktif' => '<span class="badge bg-secondary"><i class="fas fa-times-circle"></i> Non Aktif</span>',
        'isolir' => '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> Isolir</span>'
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?>">
    <title>Data Pelanggan | Admin</title>
    <!-- Gentelella Styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Gentelella-inspired styles */
        .page-title h1 {
            font-size: 24px;
            color: #2A3F54;
            margin-bottom: 5px;
        }
        .page-subtitle {
            color: #73879C;
            font-size: 14px;
        }
        .card {
            border: none;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: white;
            border-bottom: 1px solid #E5E5E5;
            padding: 15px 20px;
        }
        .card-header h6 {
            font-size: 16px;
            color: #2A3F54;
            margin: 0;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            border-top: none;
            color: #73879C;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 12px;
        }
        .table td {
            vertical-align: middle;
        }
        .btn-primary {
            background-color: #1ABB9C;
            border-color: #1ABB9C;
        }
        .btn-primary:hover {
            background-color: #169F85;
            border-color: #169F85;
        }
        .alert {
            border-left: 4px solid;
        }
        .alert-danger {
            border-left-color: #d73925;
        }
        .alert-success {
            border-left-color: #26B99A;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }
        .empty-state i {
            font-size: 50px;
            color: #73879C;
            margin-bottom: 15px;
        }
        .badge {
            padding: 5px 8px;
            font-weight: 500;
            font-size: 12px;
        }
        .badge-success {
            background-color: #26B99A;
        }
        .badge-danger {
            background-color: #d73925;
        }
        .badge-warning {
            background-color: #e08e0b;
        }
        .filter-section {
            background-color: white;
            padding: 15px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .force-down {
            margin-top: 28px;
        }
        .btn-group-sm .btn {
            margin-right: 2px;
        }
        .btn-group-sm .btn:last-child {
            margin-right: 0;
        }
        .warning-indicator {
            color: #dc3545;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <main class="main-content" id="mainContent">
        <div class="page-title">
            <h1>Data Pelanggan</h1>
            <p class="page-subtitle">Kelola data pelanggan internet</p>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <a href="../pelanggan/tambah_pelanggan.php" class="btn btn-primary mb-3">
                    <i class="fas fa-plus me-1"></i>Tambah Pelanggan
                </a>

                <!-- Alert Messages -->
                <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center mb-3">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success d-flex align-items-center mb-3">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <div class="filter-section">
                    <form method="GET" class="row g-3">
                        <div class="col-12 col-md-4 col-lg-3">
                            <label for="search_input" class="form-label">Pencarian</label>
                            <input type="text" name="search" id="search_input" class="form-control"
                                   placeholder="Nama, alamat, telepon, username..."
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>

                        <div class="col-6 col-md-4 col-lg-3">
                            <label for="status_select" class="form-label">Status</label>
                            <select name="status" id="status_select" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="aktif" <?= $status_filter === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= $status_filter === 'nonaktif' ? 'selected' : '' ?>>Non Aktif</option>
                                <option value="isolir" <?= $status_filter === 'isolir' ? 'selected' : '' ?>>Isolir</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-4 col-lg-3">
                            <label for="paket_select" class="form-label">Paket</label>
                            <select name="paket" id="paket_select" class="form-select">
                                <option value="">Semua Paket</option>
                                <?php foreach ($paket_options as $paket): ?>
                                    <option value="<?= $paket['id_paket'] ?>" <?= $paket_filter == $paket['id_paket'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($paket['nama_paket']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-lg-3">
                            <div class="d-flex gap-2 w-100 force-down">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="data_pelanggan.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-table me-2"></i>Data Pelanggan 
                            <span class="text-muted">(<?= $total_pelanggan ?> total)</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($pelanggan_list)): ?>
                            <div class="empty-state">
                                <i class="fas fa-users"></i>
                                <h5 class="text-muted">Tidak ada data pelanggan</h5>
                                <p class="text-muted mb-3">Silakan tambah pelanggan baru untuk memulai.</p>
                                <a href="../pelanggan/tambah_pelanggan.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Tambah Pelanggan
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-pelanggan">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th style="width: 30%;">Nama Pelanggan</th> 
                                            <th style="width: 15%;">Paket</th>
                                            <th style="width: 13%;">PPPoE</th>
                                            <th style="width: 10%;">Status</th>
                                            <th style="width: 12%;">Tgl Daftar</th>
                                            <th style="width: 15%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = $offset + 1;
                                        foreach ($pelanggan_list as $pelanggan): 
                                        ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <div class="fw-bold">
                                                        <?= htmlspecialchars($pelanggan['nama_pelanggan']) ?>
                                                        <?php if ($pelanggan['unpaid_bills'] > 0): ?>
                                                            <span class="warning-indicator" title="<?= $pelanggan['unpaid_bills'] ?> tagihan belum dibayar">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars(substr($pelanggan['alamat_pelanggan'], 0, 40)) ?><?= strlen($pelanggan['alamat_pelanggan']) > 40 ? '...' : '' ?>
                                                        <br><?= htmlspecialchars($pelanggan['telepon_pelanggan']) ?>
                                                    </small>
                                                </td>

                                                <td>
                                                    <?php if ($pelanggan['nama_paket']): ?>
                                                        <div class="fw-bold"><?= htmlspecialchars($pelanggan['nama_paket']) ?></div>
                                                        <small class="text-muted">
                                                            <?= format_rupiah($pelanggan['harga']) ?>
                                                        </small>
                                                    <?php else: ?>
                                                        <span class="text-muted">Belum dipilih</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($pelanggan['mikrotik_username']): ?>
                                                        <div class="fw-bold">
                                                            <?= htmlspecialchars($pelanggan['mikrotik_username']) ?>
                                                            <?php if ($pelanggan['active_connections'] > 0): ?>
                                                                <span class="text-success" title="<?= $pelanggan['active_connections'] ?> koneksi aktif">
                                                                    <i class="fas fa-wifi"></i>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <small class="text-muted"><?= htmlspecialchars($pelanggan['mikrotik_profile']) ?></small>
                                                    <?php else: ?>
                                                        <span class="text-muted">Belum diatur</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= getStatusBadge($pelanggan['status_aktif']) ?></td>
                                                <td>
                                                    <div><?= format_date($pelanggan['tgl_daftar']) ?></div>
                                                    <?php if (!empty($pelanggan['tgl_expired'])): ?>
                                                        <small class="text-muted">Exp: <?= format_date($pelanggan['tgl_expired']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group-sm">
                                                        <!-- View Button -->
                                                        <a href="detail_pelanggan.php?id=<?= $pelanggan['id_pelanggan'] ?>" 
                                                           class="btn btn-outline-info" title="Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        
                                                        <!-- Edit Button -->
                                                        <a href="edit_pelanggan.php?id=<?= $pelanggan['id_pelanggan'] ?>" 
                                                           class="btn btn-outline-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        
                                                        <!-- Delete Button - Form POST ke delete_pelanggan.php -->
                                                        <form method="POST" action="delete_pelanggan.php" style="display: inline-block;" 
                                                              onsubmit="return confirmDelete('<?= htmlspecialchars($pelanggan['nama_pelanggan']) ?>', <?= $pelanggan['unpaid_bills'] ?>, <?= $pelanggan['payment_count'] ?>, <?= $pelanggan['active_connections'] ?>);">
                                                            <input type="hidden" name="id_pelanggan" value="<?= $pelanggan['id_pelanggan'] ?>">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <button type="submit" class="btn btn-outline-danger" title="Hapus Pelanggan">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">
                                                Menampilkan <?= $offset + 1 ?> - <?= min($offset + $limit, $total_pelanggan) ?> 
                                                dari <?= $total_pelanggan ?> data
                                            </small>
                                        </div>
                                        <nav>
                                            <ul class="pagination">
                                                <?php if ($page > 1): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&paket=<?= urlencode($paket_filter) ?>">
                                                            <i class="fas fa-chevron-left"></i>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                                
                                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&paket=<?= urlencode($paket_filter) ?>">
                                                            <?= $i ?>
                                                        </a>
                                                    </li>
                                                <?php endfor; ?>
                                                
                                                <?php if ($page < $total_pages): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&paket=<?= urlencode($paket_filter) ?>">
                                                            <i class="fas fa-chevron-right"></i>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple confirm dialog for delete action
        function confirmDelete(customerName, unpaidBills, paymentHistory, activeConnections) {
            let message = `Apakah Anda yakin ingin menghapus pelanggan "${customerName}"?\n\n`;
            
            // Add warnings based on customer data
            if (unpaidBills > 0) {
                message += `⚠️ Pelanggan memiliki ${unpaidBills} tagihan yang belum dibayar!\n`;
            }
            
            if (paymentHistory > 0) {
                message += `📊 Pelanggan memiliki riwayat pembayaran yang akan ikut terhapus.\n`;
            }
            
            if (activeConnections > 0) {
                message += `🌐 Pelanggan sedang terhubung (${activeConnections} koneksi aktif).\n`;
            }
            
            message += `\n🗑️ User PPPoE akan dihapus dari Mikrotik.\n`;
            message += `📈 Data monitoring akan terhapus.\n`;
            message += `\nTindakan ini TIDAK DAPAT DIBATALKAN!`;
            
            return confirm(message);
        }

        // Auto dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // Add loading state to form buttons
        document.addEventListener('submit', function(e) {
            if (e.target.querySelector('button[type="submit"]')) {
                const submitBtn = e.target.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                submitBtn.disabled = true;
                
                // Re-enable button after 5 seconds (in case of error)
                setTimeout(function() {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            }
        });
    </script>
</body>
</html>

<?php 
require_once __DIR__ . '/../templates/footer.php';
ob_end_flush();
?>