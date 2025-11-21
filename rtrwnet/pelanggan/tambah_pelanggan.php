<?php
ob_start(); // Start output buffering at the VERY TOP

// /pelanggan/tambah_pelanggan.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load configurations
require_once __DIR__ . '/../config/config_mikrotik.php';
require_once __DIR__ . '/../config/config_database.php';
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/sidebar.php';

$error = '';
$success = '';
$paket_list = [];
$odp_list = [];

// 1. Get internet package data from database
try {
    $query = "SELECT id_paket, nama_paket, profile_name, harga, 
              CONCAT(rate_limit_rx, '/', rate_limit_tx) as kecepatan 
              FROM paket_internet WHERE status_paket = 'aktif' ORDER BY harga ASC";
    $result = $mysqli->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $paket_list[] = $row;
        }
    }
} catch (Exception $e) {
    $error = "Gagal mengambil data paket: " . $e->getMessage();
}

// 2. Get ODP data with POP info
try {
    $query = "SELECT odp.id, odp.nama_odp, odp.lokasi, odp.area_coverage,
              odc.nama_odc, pop.nama_pop, pop.id as pop_id
              FROM ftth_odp odp
              LEFT JOIN ftth_odc_ports odc_port ON odp.odc_port_id = odc_port.id
              LEFT JOIN ftth_odc odc ON odc_port.odc_id = odc.id
              LEFT JOIN ftth_pon pon ON odc.pon_port_id = pon.id
              LEFT JOIN ftth_olt olt ON pon.olt_id = olt.id
              LEFT JOIN ftth_pop pop ON olt.pop_id = pop.id
              WHERE odp.status = 'active'
              ORDER BY pop.nama_pop, odc.nama_odc, odp.nama_odp";
    $result = $mysqli->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $odp_list[] = $row;
        }
    }
} catch (Exception $e) {
    $error = "Gagal mengambil data ODP: " . $e->getMessage();
}

// 3. Generate ID Tagihan otomatis
function generateTagihanId() {
    global $mysqli;
    
    $prefix = 'INV' . date('Ym'); // Format: INV202507
    
    // Cari nomor urut terakhir untuk bulan ini
    $query = "SELECT id_tagihan FROM tagihan 
              WHERE id_tagihan LIKE '{$prefix}%' 
              ORDER BY id_tagihan DESC LIMIT 1";
    $result = $mysqli->query($query);
    
    if ($result && $result->num_rows > 0) {
        $last_id = $result->fetch_assoc()['id_tagihan'];
        $last_number = (int)substr($last_id, -4); // Ambil 4 digit terakhir
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    
    return $prefix . sprintf('%04d', $new_number);
}

// 4. Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pelanggan = isset($_POST['nama_pelanggan']) ? trim($_POST['nama_pelanggan']) : '';
    $alamat_pelanggan = isset($_POST['alamat_pelanggan']) ? trim($_POST['alamat_pelanggan']) : '';
    $telepon_pelanggan = isset($_POST['telepon_pelanggan']) ? trim($_POST['telepon_pelanggan']) : '';
    $id_paket = isset($_POST['id_paket']) ? (int)$_POST['id_paket'] : 0;
    $odp_id = isset($_POST['odp_id']) ? (int)$_POST['odp_id'] : 0;
    $tgl_daftar = isset($_POST['tgl_daftar']) ? $_POST['tgl_daftar'] : date('Y-m-d');
    $tgl_expired = isset($_POST['tgl_expired']) ? $_POST['tgl_expired'] : '';
    $mikrotik_username = isset($_POST['mikrotik_username']) ? trim($_POST['mikrotik_username']) : '';
    $mikrotik_password = isset($_POST['mikrotik_password']) ? trim($_POST['mikrotik_password']) : '';

    // Validation
    if (empty($nama_pelanggan) || empty($alamat_pelanggan) || empty($telepon_pelanggan) || 
        $id_paket <= 0 || $odp_id <= 0 || empty($tgl_daftar) || empty($tgl_expired) ||
        empty($mikrotik_username) || empty($mikrotik_password)) {
        $error = "Semua field wajib diisi!";
    } else {
        // Begin transaction
        $mysqli->begin_transaction();
        
        try {
            // Check if columns exist and add if not
            $check_columns = $mysqli->query("SHOW COLUMNS FROM data_pelanggan LIKE 'odp_id'");
            if ($check_columns->num_rows == 0) {
                $mysqli->query("ALTER TABLE data_pelanggan ADD COLUMN odp_id INT NULL AFTER id_paket");
            }
            
            $check_columns = $mysqli->query("SHOW COLUMNS FROM data_pelanggan LIKE 'pop_id'");
            if ($check_columns->num_rows == 0) {
                $mysqli->query("ALTER TABLE data_pelanggan ADD COLUMN pop_id INT NULL AFTER odp_id");
            }
            
            // Check if username already exists
            $check_username = $mysqli->prepare("SELECT id_pelanggan FROM data_pelanggan WHERE mikrotik_username = ?");
            $check_username->bind_param("s", $mikrotik_username);
            $check_username->execute();
            $result_check = $check_username->get_result();
            
            if ($result_check->num_rows > 0) {
                throw new Exception("Username {$mikrotik_username} sudah digunakan!");
            }
            
            // Get package data
            $paket_query = $mysqli->prepare("SELECT profile_name, harga FROM paket_internet WHERE id_paket = ?");
            $paket_query->bind_param("i", $id_paket);
            $paket_query->execute();
            $paket_result = $paket_query->get_result();
            
            if ($paket_result->num_rows == 0) {
                throw new Exception("Paket tidak ditemukan!");
            }
            
            $paket_data = $paket_result->fetch_assoc();
            $profile_name = $paket_data['profile_name'];
            $harga_paket = $paket_data['harga'];
            
            // Get ODP and POP data
            $odp_query = $mysqli->prepare("SELECT odp.nama_odp, pop.id as pop_id, pop.nama_pop
                                          FROM ftth_odp odp
                                          LEFT JOIN ftth_odc_ports odc_port ON odp.odc_port_id = odc_port.id
                                          LEFT JOIN ftth_odc odc ON odc_port.odc_id = odc.id
                                          LEFT JOIN ftth_pon pon ON odc.pon_port_id = pon.id
                                          LEFT JOIN ftth_olt olt ON pon.olt_id = olt.id
                                          LEFT JOIN ftth_pop pop ON olt.pop_id = pop.id
                                          WHERE odp.id = ?");
            $odp_query->bind_param("i", $odp_id);
            $odp_query->execute();
            $odp_result = $odp_query->get_result();
            
            if ($odp_result->num_rows == 0) {
                throw new Exception("ODP tidak ditemukan!");
            }
            
            $odp_data = $odp_result->fetch_assoc();
            $pop_id = $odp_data['pop_id'];
            
            // Create Mikrotik PPPoE user
            if (class_exists('RouterosAPI')) {
                $api = new RouterosAPI();
                if ($api->connect($mikrotik_ip, $mikrotik_user, $mikrotik_pass)) {
                    $api->comm('/ppp/secret/add', [
                        'name' => $mikrotik_username,
                        'password' => $mikrotik_password,
                        'profile' => $profile_name,
                        'service' => 'pppoe',
                        'comment' => "Pelanggan: {$nama_pelanggan} - ODP: {$odp_data['nama_odp']}"
                    ]);
                    $api->disconnect();
                } else {
                    throw new Exception("Gagal konek ke Mikrotik: " . (isset($api->error_str) ? $api->error_str : 'Connection failed'));
                }
            }
            
            // Save customer to database
            $insert_query = "INSERT INTO data_pelanggan 
                (nama_pelanggan, alamat_pelanggan, telepon_pelanggan, 
                 id_paket, odp_id, pop_id, tgl_daftar, tgl_expired, mikrotik_username, mikrotik_password, 
                 mikrotik_profile, sync_mikrotik, last_sync) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'yes', NOW())";
            
            $stmt = $mysqli->prepare($insert_query);
            $stmt->bind_param("sssiiiissss", 
                $nama_pelanggan, $alamat_pelanggan, $telepon_pelanggan,
                $id_paket, $odp_id, $pop_id, $tgl_daftar, $tgl_expired, $mikrotik_username, $mikrotik_password,
                $profile_name
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Gagal simpan pelanggan ke database: " . $mysqli->error);
            }
            
            $pelanggan_id = $mysqli->insert_id;
            
            // Generate ID tagihan otomatis
            $id_tagihan = generateTagihanId();
            
            // Create first invoice
            $bulan_daftar = (int)date('m', strtotime($tgl_daftar));
            $tahun_daftar = (int)date('Y', strtotime($tgl_daftar));
            $tgl_jatuh_tempo = $tgl_expired;
            
            $tagihan_query = "INSERT INTO tagihan 
                (id_tagihan, id_pelanggan, bulan_tagihan, tahun_tagihan, jumlah_tagihan, 
                 tgl_jatuh_tempo, status_tagihan, deskripsi, auto_generated, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'belum_bayar', ?, 'no', NOW())";
            
            $deskripsi_tagihan = "Tagihan perdana pelanggan {$nama_pelanggan} - " . date('F Y', strtotime($tgl_daftar));
            
            $tagihan_stmt = $mysqli->prepare($tagihan_query);
            $tagihan_stmt->bind_param("siiiiss", 
                $id_tagihan, $pelanggan_id, $bulan_daftar, $tahun_daftar, 
                $harga_paket, $tgl_jatuh_tempo, $deskripsi_tagihan
            );
            
            if (!$tagihan_stmt->execute()) {
                throw new Exception("Gagal membuat tagihan: " . $mysqli->error);
            }
            
            // Commit transaction
            $mysqli->commit();
            
            $success = "Pelanggan {$nama_pelanggan} berhasil ditambahkan! User PPPoE sudah dibuat di Mikrotik dan tagihan pertama sudah digenerate.";
            
            // Clear form data
            $_POST = array();
            
            // Redirect after 3 seconds
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'data_pelanggan.php';
                }, 3000);
            </script>";
            
        } catch (Exception $e) {
            // Rollback transaction
            $mysqli->rollback();
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Gentelella Style -->
    <style>
    :root {
      --primary: #1ABB9C;
      --success: #26B99A;
      --info: #23C6C8;
      --warning: #F8AC59;
      --danger: #ED5565;
      --secondary: #73879C;
      --dark: #2A3F54;
      --light: #F7F7F7;
    }

    body {
      background-color: #F7F7F7;
      font-family: "Segoe UI", Roboto, Arial, sans-serif;
      color: #73879C;
    }

    .main-content {
      padding: 20px;
      margin-left: 220px;
      min-height: calc(100vh - 52px);
    }

    .page-title {
      margin-bottom: 30px;
    }

    .page-title h1 {
      font-size: 24px;
      color: var(--dark);
      margin: 0;
      font-weight: 400;
    }

    .page-title .page-subtitle {
      color: var(--secondary);
      font-size: 13px;
      margin: 5px 0 0 0;
    }

    .card {
      border: none;
      border-radius: 5px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      margin-bottom: 20px;
    }

    .card-header {
      background-color: white;
      border-bottom: 1px solid #e5e5e5;
      padding: 15px 20px;
      border-radius: 5px 5px 0 0 !important;
    }

    .card-header h5 {
      font-size: 16px;
      font-weight: 500;
      color: var(--dark);
      margin: 0;
    }

    .settings-section {
      background: white;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 5px;
      border: 1px solid #e5e5e5;
    }

    .settings-section h6 {
      font-size: 14px;
      font-weight: 600;
      color: var(--dark);
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }

    .form-label {
      font-weight: 500;
      font-size: 13px;
      color: #555;
      margin-bottom: 5px;
    }

    .form-control, .form-select {
      border-radius: 3px;
      border: 1px solid #D5D5D5;
      font-size: 13px;
      padding: 8px 12px;
      height: auto;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.2rem rgba(26, 187, 156, 0.25);
    }

    .required {
      color: var(--danger);
      font-weight: bold;
    }

    .btn {
      border-radius: 3px;
      font-size: 13px;
      padding: 8px 15px;
      font-weight: 500;
    }

    .btn-primary {
      background-color: var(--primary);
      border-color: var(--primary);
    }

    .btn-primary:hover {
      background-color: #169F85;
      border-color: #169F85;
    }

    .btn-outline-secondary {
      color: var(--secondary);
      border-color: var(--secondary);
    }

    .btn-outline-secondary:hover {
      background-color: var(--secondary);
      color: white;
    }

    .btn-success {
      background-color: var(--success);
      border-color: var(--success);
    }

    .alert {
      padding: 10px 15px;
      font-size: 13px;
      border-radius: 3px;
      border: none;
    }

    .alert-danger {
      background-color: rgba(237, 85, 101, 0.1);
      color: var(--danger);
    }

    .alert-success {
      background-color: rgba(38, 185, 154, 0.1);
      color: var(--success);
    }

    .alert-info {
      background-color: rgba(35, 198, 200, 0.1);
      color: var(--info);
    }

    .input-group-text {
      background-color: #f5f5f5;
      border: 1px solid #ddd;
      font-size: 13px;
    }

    .form-select {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
      background-size: 12px 12px;
      padding: 8px 30px 8px 12px;
    }

    @media (max-width: 992px) {
      .main-content {
        margin-left: 0;
        padding: 15px;
      }
    }

    .odp-info {
      font-size: 11px;
      color: #666;
      margin-top: 5px;
    }

    .pop-display {
      background-color: #f8f9fa;
      padding: 10px;
      border-radius: 3px;
      border: 1px solid #dee2e6;
      margin-top: 10px;
    }
    </style>
</head>
<body>
    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="page-title">
            <h1>Tambah Pelanggan Baru</h1>
            <p class="page-subtitle">Formulir pendaftaran pelanggan baru dengan integrasi Mikrotik</p>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Form Tambah Pelanggan</h5>
                        <a href="data_pelanggan.php" class="btn btn-sm btn-success">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" id="formTambahPelanggan">
                            <!-- Section 1: Data Pribadi Pelanggan -->
                            <div class="settings-section">
                                <h6><i class="fas fa-user me-2"></i>Data Pribadi Pelanggan</h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nama_pelanggan" class="form-label">Nama Lengkap <span class="required">*</span></label>
                                            <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan"
                                                   placeholder="Masukkan nama lengkap pelanggan" 
                                                   value="<?= isset($_POST['nama_pelanggan']) ? htmlspecialchars($_POST['nama_pelanggan']) : '' ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="telepon_pelanggan" class="form-label">No. Telepon <span class="required">*</span></label>
                                            <input type="tel" class="form-control" id="telepon_pelanggan" name="telepon_pelanggan"
                                                   placeholder="08xxxxxxxxxx" 
                                                   value="<?= isset($_POST['telepon_pelanggan']) ? htmlspecialchars($_POST['telepon_pelanggan']) : '' ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="alamat_pelanggan" class="form-label">Alamat Lengkap <span class="required">*</span></label>
                                            <textarea class="form-control" id="alamat_pelanggan" name="alamat_pelanggan"
                                                      rows="3" placeholder="Masukkan alamat lengkap dengan RT/RW" required><?= isset($_POST['alamat_pelanggan']) ? htmlspecialchars($_POST['alamat_pelanggan']) : '' ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tgl_daftar" class="form-label">Tanggal Daftar <span class="required">*</span></label>
                                            <input type="date" class="form-control" id="tgl_daftar" name="tgl_daftar"
                                                   value="<?= isset($_POST['tgl_daftar']) ? $_POST['tgl_daftar'] : date('Y-m-d') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tgl_expired" class="form-label">Tanggal Expired <span class="required">*</span></label>
                                            <input type="date" class="form-control" id="tgl_expired" name="tgl_expired"
                                                   value="<?= isset($_POST['tgl_expired']) ? $_POST['tgl_expired'] : date('Y-m-d', strtotime('+30 days')) ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Lokasi Jaringan -->
                            <div class="settings-section">
                                <h6><i class="fas fa-map-marker-alt me-2"></i>Lokasi Jaringan</h6>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="odp_id" class="form-label">Pilih ODP <span class="required">*</span></label>
                                            <select class="form-select" id="odp_id" name="odp_id" required onchange="updatePOP()">
                                                <option value="">-- Pilih ODP --</option>
                                                <?php foreach ($odp_list as $odp): ?>
                                                    <option value="<?= $odp['id'] ?>" 
                                                            data-pop-id="<?= $odp['pop_id'] ?>"
                                                            data-pop-name="<?= htmlspecialchars($odp['nama_pop']) ?>"
                                                            data-odc-name="<?= htmlspecialchars($odp['nama_odc']) ?>"
                                                            data-area="<?= htmlspecialchars($odp['area_coverage']) ?>"
                                                            <?= (isset($_POST['odp_id']) && $_POST['odp_id'] == $odp['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($odp['nama_odp']) ?> - <?= htmlspecialchars($odp['lokasi']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">POP Terhubung</label>
                                            <div class="pop-display" id="popDisplay">
                                                <i class="fas fa-building me-2"></i>
                                                <span id="popName">Pilih ODP terlebih dahulu</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">ODC Terhubung</label>
                                            <div class="pop-display">
                                                <i class="fas fa-server me-2"></i>
                                                <span id="odcName">Pilih ODP terlebih dahulu</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Area Coverage</label>
                                            <div class="pop-display">
                                                <i class="fas fa-map-marked-alt me-2"></i>
                                                <span id="areaName">Pilih ODP terlebih dahulu</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Paket Internet -->
                            <div class="settings-section">
                                <h6><i class="fas fa-wifi me-2"></i>Paket Internet</h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="id_paket" class="form-label">Pilih Paket <span class="required">*</span></label>
                                            <select class="form-select" id="id_paket" name="id_paket" required onchange="updateHarga()">
                                                <option value="">-- Pilih Paket Internet --</option>
                                                <?php foreach ($paket_list as $paket): ?>
                                                    <option value="<?= $paket['id_paket'] ?>" 
                                                            data-harga="<?= $paket['harga'] ?>"
                                                            data-kecepatan="<?= htmlspecialchars($paket['kecepatan']) ?>"
                                                            <?= (isset($_POST['id_paket']) && $_POST['id_paket'] == $paket['id_paket']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($paket['nama_paket']) ?> - <?= htmlspecialchars($paket['kecepatan']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="harga_display" class="form-label">Harga Paket</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control" id="harga_display" 
                                                       placeholder="Pilih paket terlebih dahulu" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Konfigurasi PPPoE Mikrotik -->
                            <div class="settings-section">
                                <h6><i class="fas fa-cogs me-2"></i>Konfigurasi PPPoE Mikrotik</h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mikrotik_username" class="form-label">Username PPPoE <span class="required">*</span></label>
                                            <input type="text" class="form-control" id="mikrotik_username" name="mikrotik_username"
                                                   placeholder="Username untuk PPPoE" 
                                                   value="<?= isset($_POST['mikrotik_username']) ? htmlspecialchars($_POST['mikrotik_username']) : '' ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mikrotik_password" class="form-label">Password PPPoE <span class="required">*</span></label>
                                            <input type="text" class="form-control" id="mikrotik_password" name="mikrotik_password"
                                                   placeholder="Password untuk PPPoE" 
                                                   value="<?= isset($_POST['mikrotik_password']) ? htmlspecialchars($_POST['mikrotik_password']) : '' ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-3 mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Username dan password ini akan digunakan pelanggan untuk koneksi PPPoE di Mikrotik. Tagihan perdana akan otomatis dibuat setelah pelanggan berhasil ditambahkan.
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Simpan Pelanggan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Update POP info when ODP is selected
        function updatePOP() {
            const odpSelect = document.getElementById('odp_id');
            const popDisplay = document.getElementById('popName');
            const odcName = document.getElementById('odcName');
            const areaName = document.getElementById('areaName');
            
            if (odpSelect.value === '') {
                popDisplay.textContent = 'Pilih ODP terlebih dahulu';
                odcName.textContent = 'Pilih ODP terlebih dahulu';
                areaName.textContent = 'Pilih ODP terlebih dahulu';
            } else {
                const selectedOption = odpSelect.options[odpSelect.selectedIndex];
                const popName = selectedOption.getAttribute('data-pop-name');
                const odcNameValue = selectedOption.getAttribute('data-odc-name');
                const areaValue = selectedOption.getAttribute('data-area');
                
                popDisplay.textContent = popName || 'Tidak terhubung ke POP';
                odcName.textContent = odcNameValue || 'N/A';
                areaName.textContent = areaValue || 'N/A';
            }
        }

        // Update price automatically when package is selected
        function updateHarga() {
            const paketSelect = document.getElementById('id_paket');
            const hargaDisplay = document.getElementById('harga_display');
            
            if (paketSelect.value === '') {
                hargaDisplay.value = '';
                hargaDisplay.placeholder = 'Pilih paket terlebih dahulu';
            } else {
                const selectedOption = paketSelect.options[paketSelect.selectedIndex];
                const harga = selectedOption.getAttribute('data-harga');
                const kecepatan = selectedOption.getAttribute('data-kecepatan');
                
                // Format price with thousand separators
                const hargaFormatted = new Intl.NumberFormat('id-ID').format(harga);
                hargaDisplay.value = hargaFormatted + ' / bulan (' + kecepatan + ')';
            }
        }

        // Auto set expired date 30 days from registration date
        document.getElementById('tgl_daftar').addEventListener('change', function() {
            const tglDaftar = new Date(this.value);
            if (!isNaN(tglDaftar.getTime())) {
                // Add 30 days
                tglDaftar.setDate(tglDaftar.getDate() + 30);
                
                // Format to YYYY-MM-DD
                const year = tglDaftar.getFullYear();
                const month = String(tglDaftar.getMonth() + 1).padStart(2, '0');
                const day = String(tglDaftar.getDate()).padStart(2, '0');
                
                document.getElementById('tgl_expired').value = `${year}-${month}-${day}`;
            }
        });

        // Form validation before submission
        document.getElementById('formTambahPelanggan').addEventListener('submit', function(e) {
            const username = document.getElementById('mikrotik_username').value;
            const password = document.getElementById('mikrotik_password').value;
            const telepon = document.getElementById('telepon_pelanggan').value;
            const odpId = document.getElementById('odp_id').value;
            
            // Validate username length
            if (username.length < 4) {
                alert('Username minimal 4 karakter!');
                document.getElementById('mikrotik_username').focus();
                e.preventDefault();
                return;
            }
            
            // Validate password length
            if (password.length < 6) {
                alert('Password minimal 6 karakter!');
                document.getElementById('mikrotik_password').focus();
                e.preventDefault();
                return;
            }
            
            // Validate phone number
            if (!/^[0-9+\-\s()]+$/.test(telepon)) {
                alert('Nomor telepon hanya boleh berisi angka, +, -, spasi, dan tanda kurung!');
                document.getElementById('telepon_pelanggan').focus();
                e.preventDefault();
                return;
            }
            
            // Validate ODP selection
            if (odpId === '') {
                alert('Silakan pilih ODP terlebih dahulu!');
                document.getElementById('odp_id').focus();
                e.preventDefault();
                return;
            }
            
            // Confirm submission
            if (!confirm('Apakah Anda yakin ingin menambahkan pelanggan ini? Data akan langsung dibuat di Mikrotik.')) {
                e.preventDefault();
                return;
            }
        });

        // Initialize displays if values are already selected (after error)
        document.addEventListener('DOMContentLoaded', function() {
            updateHarga();
            updatePOP();
        });

        // Format phone number input
        document.getElementById('telepon_pelanggan').addEventListener('input', function() {
            // Remove non-numeric characters except +, -, space, and parentheses
            this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
        });
    </script>
</body>
</html>

<?php 
require_once __DIR__ . '/../templates/footer.php';
ob_end_flush(); // End output buffering
?>