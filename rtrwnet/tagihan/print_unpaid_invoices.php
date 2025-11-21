<?php
// /tagihan/print_unpaid_invoices.php - REVISED VERSION with data_pelanggan integration

ob_start(); // Start output buffering at the VERY TOP

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Load configurations
require_once __DIR__ . '/../config/config_database.php';

// Database connection already available from config_database.php as $mysqli

// Get company settings
$companySettings = [];
try {
    $query = "SELECT * FROM pengaturan_perusahaan LIMIT 1";
    $result = $mysqli->query($query);
    
    if ($result && $result->num_rows > 0) {
        $companySettings = $result->fetch_assoc();
    }
} catch (Exception $e) {
    // Fallback to default values if error occurs
    $companySettings = [
        'nama_perusahaan' => 'PT. Area Near Urban Netsindo (ANUNET)',
        'alamat_perusahaan' => 'Jl. Tirta Atmaja RT. 003 RW. 001, Desa Tugubandung, Kec. Kabandungan, Kab. Sukabumi - 43368',
        'telepon_perusahaan' => '0812-9513-6503',
        'email_perusahaan' => '',
        'bank_nama' => 'Bank BRI PALASARI GIRANG',
        'bank_atas_nama' => 'YANI MASLIAN',
        'bank_no_rekening' => '4098-0104-1754-534',
        'logo_perusahaan' => 'logo_ljn.png'
    ];
}

// Calculate invoice amount
function calculateInvoiceAmount($customer, $harga_paket) {
    $amount = $harga_paket - (isset($customer['diskon']) ? $customer['diskon'] : 0);
    return ['amount' => $amount];
}

// Get all customers with their package information
try {
    $query_select = "SELECT 
                        dp.*, 
                        pi.nama_paket, 
                        pi.harga as harga_paket
                     FROM 
                        data_pelanggan dp 
                     LEFT JOIN 
                        paket_internet pi ON dp.id_paket = pi.id_paket 
                     WHERE 
                        dp.status_aktif = 'aktif'
                     ORDER BY 
                        dp.nama_pelanggan ASC";
    
    $result = $mysqli->query($query_select);
    
    if (!$result) {
        throw new Exception("Query failed: " . $mysqli->error);
    }
    
    $customers = $result->fetch_all(MYSQLI_ASSOC);
    
    // Calculate total amount
    $total_tagihan = 0;
    foreach ($customers as $cust) {
        $invoice = calculateInvoiceAmount($cust, $cust['harga_paket'] ?? 0);
        $total_tagihan += $invoice['amount'];
    }

} catch (Exception $e) { 
    die("<div class='alert alert-danger'>Database Error: " . $e->getMessage() . "</div>");
}
?>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Semua Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 5mm;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            background-color: white;
            padding: 0;
            margin: 0;
        }
        
        .page {
            height: 287mm;
            width: 200mm;
            page-break-after: always;
            position: relative;
            padding: 0;
            margin: 0;
        }
        
        .invoice-container {
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
        }
        
        .invoice-card {
            border: 1px solid #000;
            padding: 10px;
            height: 138mm;
            margin-bottom: 5mm;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        
        .invoice-card:last-child {
            margin-bottom: 0;
        }
        
        @media print {
            body {
                padding: 0;
                margin: 0;
                font-size: 12px;
            }
            .no-print {
                display: none !important;
            }
            .page {
                padding: 0;
                margin: 0;
                page-break-after: always;
            }
            .invoice-card {
                border: 1px solid #000;
                padding: 10px;
                height: 138mm;
                margin-bottom: 5mm;
            }
            .invoice-card:last-child {
                margin-bottom: 0;
            }
        }
        
        .invoice-header {
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        
        .invoice-title {
            color: #000;
            font-size: 16px;
            font-weight: bold;
            margin: 8px 0;
        }
        
        .company-info {
            font-size: 10px;
            line-height: 1.4;
        }
        
        .customer-info {
            font-size: 11px;
            line-height: 1.4;
        }
        
        .table {
            width: 100%;
            margin-bottom: 8px;
            border-collapse: collapse;
            font-size: 10px;
        }
        
        .table th, .table td {
            padding: 4px;
            border: 1px solid #333;
        }
        
        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .status-unpaid {
            color: #dc3545;
            font-weight: bold;
            font-size: 11px;
        }
        
        .status-paid {
            color: #28a745;
            font-weight: bold;
            font-size: 11px;
        }
        
        .logo { 
            max-height: 45px;
            width: auto;
            margin-bottom: 4px;
        }
        
        .payment-info {
            border: 1px solid #333;
            padding: 6px;
            margin-top: 6px;
            font-size: 9px;
            background-color: #f9f9f9;
            line-height: 1.3;
        }
        
        .text-muted {
            color: #6c757d;
            font-size: 9px;
        }
        
        .badge {
            font-size: 75%;
        }
        
        .small {
            font-size: 9px;
        }
        
        .text-danger {
            font-size: 10px;
        }
        
        .table .text-right {
            text-align: right;
        }

        /* Enhanced styling for print */
        .print-header {
            background: linear-gradient(135deg, #1ABB9C 0%, #16a085 100%);
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .print-summary {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #1ABB9C;
        }
        
        .total-row {
            background-color: #f8f9fa !important;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="no-print print-header">
        <h4><i class="bi bi-printer"></i> Cetak Semua Invoice</h4>
        <p class="mb-0">Sistem Billing Management - <?= htmlspecialchars($companySettings['nama_perusahaan'] ?? 'PT. Area Near Urban Netsindo') ?></p>
    </div>

    <div class="no-print print-summary">
        <div class="row">
            <div class="col-md-8">
                <button onclick="window.print()" class="btn btn-primary btn-sm me-2">
                    <i class="bi bi-printer"></i> Cetak Semua
                </button>
                <a href="data_tagihan.php" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali ke Data Tagihan
                </a>
            </div>
            <div class="col-md-4 text-end">
                <strong>Total Pelanggan: <?= count($customers) ?></strong><br>
                <strong>Total Tagihan: Rp <?= number_format($total_tagihan, 0, ',', '.') ?></strong>
            </div>
        </div>
        
        <div class="mt-2">
            <small class="text-success">
                <i class="bi bi-info-circle"></i> 
                Menampilkan pelanggan aktif (filtered by status_aktif = 'aktif')
            </small>
        </div>
    </div>

    <?php if (empty($customers)): ?>
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i>
            Tidak ada data pelanggan yang ditemukan.
        </div>
    <?php else: ?>
        <?php
        $customerCount = count($customers);
        $invoicesPerPage = 2;
        $pages = ceil($customerCount / $invoicesPerPage);

        for ($page = 0; $page < $pages; $page++):
            $startIdx = $page * $invoicesPerPage;
            $currentCustomers = array_slice($customers, $startIdx, $invoicesPerPage);
        ?>
        <div class="page">
            <div class="invoice-container">
                <?php foreach ($currentCustomers as $idx => $customer): ?>
                <div class="invoice-card">
                    <div class="invoice-header">
                        <div class="row align-items-start">
                            <div class="col-6">
                                <?php if (!empty($companySettings['logo_perusahaan'])): ?>
                                <img src="<?= htmlspecialchars($companySettings['logo_perusahaan']) ?>" alt="Logo Perusahaan" class="logo">
                                <?php endif; ?>
                                <div class="company-info">
                                    <strong><?= htmlspecialchars($companySettings['nama_perusahaan'] ?? 'PT. Area Near Urban Netsindo (ANUNET)') ?></strong><br>
                                    <?= nl2br(htmlspecialchars($companySettings['alamat_perusahaan'] ?? 'Jl. Tirta Atmaja RT. 003 RW. 001, Desa Tugubandung, Kec. Kabandungan, Kab. Sukabumi - 43368')) ?><br>
                                    <?php if (!empty($companySettings['telepon_perusahaan'])): ?>
                                    Hotline Service: <?= htmlspecialchars($companySettings['telepon_perusahaan']) ?> <br>
                                    <?php endif; ?>
                                    <?php if (!empty($companySettings['email_perusahaan'])): ?>
                                    Email: <?= htmlspecialchars($companySettings['email_perusahaan']) ?> <br>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="invoice-title text-end">INVOICE TAGIHAN</div>
                                <div class="text-muted small">
                                    No: INV-<?= date('Ym') ?>-<?= str_pad($customer['id_pelanggan'], 4, '0', STR_PAD_LEFT) ?><br>
                                    Tanggal: <?= date('d M Y') ?>
                                </div>
                                <div class="customer-info text-end mt-1">
                                    <strong>Kepada Yth:</strong><br>
                                    <strong><?= htmlspecialchars($customer['nama_pelanggan']) ?></strong><br>
                                    <?= htmlspecialchars($customer['alamat_pelanggan']) ?><br>
                                    Telp: <?= htmlspecialchars($customer['telepon_pelanggan']) ?><br>
                                    <?php if (!empty($customer['email_pelanggan'])): ?>
                                    Email: <?= htmlspecialchars($customer['email_pelanggan']) ?><br>
                                    <?php endif; ?>
                                    <span class="badge bg-secondary">ID: <?= $customer['id_pelanggan'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <td width="35%"><strong>Paket Internet</strong></td>
                            <td><?= htmlspecialchars($customer['nama_paket'] ?? 'Paket tidak tersedia') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Periode Tagihan</strong></td>
                            <td>
                                <?= date('d M Y', strtotime($customer['tgl_expired'])) ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status Pembayaran</strong></td>
                            <td class="status-unpaid">
                                BELUM LUNAS
                            </td>
                        </tr>
                        <?php if (!empty($customer['last_paid_date'])): ?>
                        <tr>
                            <td><strong>Terakhir Bayar</strong></td>
                            <td>
                                <?= date('d M Y', strtotime($customer['last_paid_date'])) ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>

                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th width="70%">Deskripsi Layanan</th>
                                <th width="30%">Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>Tagihan Internet <?= htmlspecialchars($customer['nama_paket'] ?? 'Paket Internet') ?></strong><br>
                                    <small>Periode: <?= date('d/m/Y', strtotime($customer['tgl_expired'])) ?></small>
                                </td>
                                <td class="text-end">Rp. <?= number_format($customer['harga_paket'] ?? 0, 0, ',', '.') ?></td>
                            </tr>
                            <?php if (isset($customer['diskon']) && $customer['diskon'] > 0): ?>
                            <tr>
                                <td>Diskon</td> 
                                <td class="text-end">- Rp. <?= number_format($customer['diskon'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="total-row">
                                <td class="text-end"><strong>TOTAL TAGIHAN</strong></td>
                                <td class="text-end"> 
                                    <strong>Rp. <?= number_format(
                                        ($customer['harga_paket'] ?? 0) - (isset($customer['diskon']) ? $customer['diskon'] : 0), 
                                        0, ',', '.'
                                    ) ?></strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="payment-info small">
                        <div class="row">
                            <div class="col-8">
                                <strong>INFORMASI PEMBAYARAN:</strong><br>
                                <div class="mt-1">
                                    <?php if (!empty($companySettings['bank_nama'])): ?>
                                    <strong>Transfer Bank:</strong><br>
                                    <?= htmlspecialchars($companySettings['bank_nama']) ?><br>
                                    No. Rek: <?= htmlspecialchars($companySettings['bank_no_rekening'] ?? '') ?><br>
                                    a.n. <?= htmlspecialchars($companySettings['bank_atas_nama'] ?? '') ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <strong>Batas Pembayaran:</strong><br>
                                <span class="text-danger">
                                    <?= date('d M Y', strtotime($customer['tgl_expired'])) ?>
                                </span>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-12">
                                <div class="text-muted">
                                    <?php if (!empty($companySettings['telepon_perusahaan'])): ?>
                                    * Konfirmasi pembayaran: WA <?= htmlspecialchars($companySettings['telepon_perusahaan']) ?><br>
                                    <?php endif; ?>
                                    * Layanan akan otomatis terputus jika melewati batas pembayaran<br>
                                    * Print: <?= date('d/m/Y H:i') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (count($currentCustomers) == 1): ?>
                    <div class="invoice-card" style="border: none; height: 138mm;">
                        <div class="text-center text-muted" style="padding-top: 60mm;">
                            <i class="bi bi-file-earmark-text" style="font-size: 48px; opacity: 0.3;"></i><br>
                            <small>Halaman kosong - hanya 1 invoice pada halaman ini</small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endfor; ?>
    <?php endif; ?>

    <div class="no-print" style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <div class="row">
            <div class="col-md-6">
                <h6><i class="bi bi-info-circle"></i> Informasi Cetak:</h6>
                <ul class="small mb-0">
                    <li>Format: A4 Portrait (2 invoice per halaman)</li>
                    <li>Total Invoice: <?= count($customers) ?> buah</li>
                    <li>Total Halaman: <?= $pages ?> halaman</li>
                    <li>Tanggal Cetak: <?= date('d M Y H:i:s') ?></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6><i class="bi bi-person-check"></i> Dicetak oleh:</h6>
                <ul class="small mb-0">
                    <li>User: <?= htmlspecialchars($_SESSION['username'] ?? 'Unknown') ?></li>
                    <li>Sistem: Billing Management <?= htmlspecialchars($companySettings['nama_perusahaan'] ?? 'ANUNET') ?></li>
                    <li>Feature: Print All Invoices</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Auto print if requested
            if (window.location.search.indexOf('autoprint') > -1) {
                setTimeout(function() {
                    window.print();
                    if (window.location.search.indexOf('autoclose') > -1) {
                        setTimeout(window.close, 500);
                    }
                }, 1000);
            }
        };
        
        // Print shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
        
        // Print completion tracking
        window.addEventListener('beforeprint', function() {
            console.log('Print dialog opened for <?= count($customers) ?> invoices');
        });
        
        window.addEventListener('afterprint', function() {
            console.log('Print dialog closed');
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>