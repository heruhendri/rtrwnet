<?php
// payment.php - FIXED VERSION with correct column names and keterangan

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
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/sidebar.php';

// Database connection already available from config_database.php as $mysqli

class InvoicePayment {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function getInvoiceDetail($invoiceId) {
        // FIXED: Updated column names to match actual database structure
        $stmt = $this->mysqli->prepare("SELECT t.*, 
                                       p.nama_pelanggan, 
                                       p.alamat_pelanggan as alamat, 
                                       p.telepon_pelanggan as no_telp, 
                                       pi.nama_paket, 
                                       pi.harga as harga_paket
                                       FROM tagihan t 
                                       JOIN data_pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                                       LEFT JOIN paket_internet pi ON p.id_paket = pi.id_paket
                                       WHERE t.id_tagihan = ?");
        $stmt->bind_param("s", $invoiceId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function processPayment($invoiceId, $metodePembayaran, $tanggalBayar, $diskon = 0) {
        // Get invoice details
        $invoice = $this->getInvoiceDetail($invoiceId);
        if (!$invoice) {
            throw new Exception("Tagihan tidak ditemukan");
        }

        if ($invoice['status_tagihan'] === 'sudah_bayar') {
            throw new Exception("Tagihan sudah dibayar sebelumnya");
        }

        // Calculate amount after discount
        $amount = $invoice['jumlah_tagihan'] - $diskon;
        
        // Start transaction
        $this->mysqli->begin_transaction();

        try {
            // Update invoice status
            $updateInvoice = $this->mysqli->prepare("UPDATE tagihan 
                                                   SET status_tagihan = 'sudah_bayar'
                                                   WHERE id_tagihan = ?");
            $updateInvoice->bind_param("s", $invoiceId);
            $updateInvoice->execute();

            // FIXED: Create detailed keterangan with customer name
            $bulanIndo = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            
            $bulanTagihan = $bulanIndo[$invoice['bulan_tagihan']];
            $tahunTagihan = $invoice['tahun_tagihan'];
            $namaPelanggan = $invoice['nama_pelanggan'];
            
            $keterangan = "Pembayaran tagihan $namaPelanggan - periode $bulanTagihan $tahunTagihan";
            if ($diskon > 0) {
                $keterangan .= " (Diskon: Rp " . number_format($diskon, 0, ',', '.') . ")";
            }

            // Insert payment record
            $insertPayment = $this->mysqli->prepare("INSERT INTO pembayaran 
                (id_tagihan, id_pelanggan, tanggal_bayar, jumlah_bayar, metode_bayar, keterangan, id_user_pencatat) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $userId = $_SESSION['user_id'] ?? null;
            
            $insertPayment->bind_param("sisdssi", 
                $invoiceId, 
                $invoice['id_pelanggan'], 
                $tanggalBayar, 
                $amount,
                $metodePembayaran,
                $keterangan,
                $userId
            );
            $insertPayment->execute();

            // ADDED: Insert into transaksi_lain for mutasi keuangan tracking
            $keteranganMutasi = "Pembayaran tagihan - $namaPelanggan ($invoiceId) - $bulanTagihan $tahunTagihan";
            if ($diskon > 0) {
                $keteranganMutasi .= " [Diskon: Rp " . number_format($diskon, 0, ',', '.') . "]";
            }
            $keteranganMutasi .= " - Metode: $metodePembayaran";

            $insertMutasi = $this->mysqli->prepare("INSERT INTO transaksi_lain 
                (tanggal, jenis, kategori, keterangan, jumlah, created_by) 
                VALUES (?, 'pemasukan', 'Pembayaran Pelanggan', ?, ?, ?)");
            
            $insertMutasi->bind_param("ssdi", 
                $tanggalBayar, 
                $keteranganMutasi, 
                $amount, 
                $userId
            );
            $insertMutasi->execute();

            // Update customer last paid date and extend expired date if needed
            $newExpiredDate = date('Y-m-d', strtotime($invoice['tgl_jatuh_tempo'] . ' +1 month'));
            $updateCustomer = $this->mysqli->prepare("UPDATE data_pelanggan 
                                                     SET last_paid_date = ?, 
                                                         tgl_expired = GREATEST(COALESCE(tgl_expired, ?), ?)
                                                     WHERE id_pelanggan = ?");
            $updateCustomer->bind_param("sssi", $tanggalBayar, $newExpiredDate, $newExpiredDate, $invoice['id_pelanggan']);
            $updateCustomer->execute();

            $this->mysqli->commit();
            return true;
        } catch (Exception $e) {
            $this->mysqli->rollback();
            throw $e;
        }
    }

    private function validateDate($date) {
        if (empty($date)) return false;
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}

// Validate invoice ID
if (!isset($_GET['invoice_id']) || empty($_GET['invoice_id'])) {
    $_SESSION['error_message'] = 'ID Tagihan tidak valid';
    header("Location: data_tagihan.php");
    exit;
}
$invoiceId = $_GET['invoice_id'];

// Initialize payment handler
$paymentHandler = new InvoicePayment($mysqli);

// Process payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate inputs
        $metodePembayaran = isset($_POST['metode_pembayaran']) ? trim($_POST['metode_pembayaran']) : '';
        $tanggalBayar = isset($_POST['tanggal_bayar']) ? trim($_POST['tanggal_bayar']) : '';
        $diskon = isset($_POST['diskon']) ? (int)$_POST['diskon'] : 0;
        $printInvoice = isset($_POST['print_invoice']) ? $_POST['print_invoice'] : 'no';

        $errors = [];
        if (empty($metodePembayaran)) $errors[] = 'Metode pembayaran harus dipilih';
        if (empty($tanggalBayar)) $errors[] = 'Tanggal pembayaran harus diisi';
        if ($diskon < 0) $errors[] = 'Diskon tidak boleh negatif';
        
        if (!empty($errors)) {
            throw new Exception(implode('<br>', $errors));
        }

        // Process payment
        $success = $paymentHandler->processPayment($invoiceId, $metodePembayaran, $tanggalBayar, $diskon);
        
        if ($success) {
            $_SESSION['success_message'] = "Pembayaran berhasil dicatat dan telah ditambahkan ke mutasi keuangan!";
            
            // Get invoice details for invoice printing
            $invoice = $paymentHandler->getInvoiceDetail($invoiceId);
            $_SESSION['invoice_data'] = [
                'invoice' => $invoice,
                'amount' => $invoice['jumlah_tagihan'] - $diskon,
                'diskon' => $diskon,
                'metode_pembayaran' => $metodePembayaran,
                'tanggal_bayar' => $tanggalBayar,
                'invoice_number' => $invoiceId
            ];
            
            if ($printInvoice === 'yes') {
                header("Location: print_invoice.php");
            } else {
                header("Location: data_tagihan.php");
            }
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
}

// Get invoice data for display
$invoice = $paymentHandler->getInvoiceDetail($invoiceId);
if (!$invoice) {
    $_SESSION['error_message'] = 'Tagihan tidak ditemukan';
    header("Location: data_tagihan.php");
    exit;
}

// Check if already paid
if ($invoice['status_tagihan'] === 'sudah_bayar') {
    $_SESSION['error_message'] = 'Tagihan sudah dibayar sebelumnya';
    header("Location: data_tagihan.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Pembayaran</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .is-invalid { border-color: #dc3545; }
        .invalid-feedback {
            color: #dc3545;
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
        }
        .invoice-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
<div class="main-content">
    <div class="container-fluid mt-4">
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-money-bill"></i> Konfirmasi Pembayaran Tagihan</h4>
            </div>
            <div class="card-body">
                <form id="paymentForm" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="invoice-info">
                                <h5 class="mb-3"><i class="fas fa-file-invoice"></i> Detail Tagihan</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <th width="40%">ID Tagihan</th>
                                        <td><?= htmlspecialchars($invoice['id_tagihan']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nama Pelanggan</th>
                                        <td><strong><?= htmlspecialchars($invoice['nama_pelanggan']) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th>No. Telp</th>
                                        <td><?= htmlspecialchars($invoice['no_telp']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td><?= htmlspecialchars($invoice['alamat']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Paket Internet</th>
                                        <td><?= htmlspecialchars($invoice['nama_paket']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Periode Tagihan</th>
                                        <td><?= date('F Y', strtotime($invoice['tahun_tagihan'] . '-' . $invoice['bulan_tagihan'] . '-01')) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Jatuh Tempo</th>
                                        <td><?= date('d/m/Y', strtotime($invoice['tgl_jatuh_tempo'])) ?></td>
                                    </tr>
                                    <tr class="table-warning">
                                        <th>Jumlah Tagihan</th>
                                        <td><strong>Rp <span id="original_amount"><?= number_format($invoice['jumlah_tagihan'], 0, ',', '.') ?></span></strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="mb-3"><i class="fas fa-credit-card"></i> Detail Pembayaran</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_bayar" value="<?= date('Y-m-d') ?>" required max="<?= date('Y-m-d') ?>">
                                <div class="invalid-feedback">Harap isi tanggal pembayaran</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Diskon (Rp)</label>
                                <input type="number" class="form-control" name="diskon" id="diskon" value="0" min="0" max="<?= $invoice['jumlah_tagihan'] ?>">
                                <div class="invalid-feedback">Diskon tidak valid</div>
                                <small class="text-muted">Maksimal: Rp <?= number_format($invoice['jumlah_tagihan'], 0, ',', '.') ?></small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select class="form-select" name="metode_pembayaran" required>
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="CASH">CASH</option>
                                    <option value="TRANSFER">TRANSFER</option>
                                    <option value="E-WALLET">E-WALLET</option>
                                    <option value="QRIS">QRIS</option>
                                </select>
                                <div class="invalid-feedback">Harap pilih metode pembayaran</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Total yang Harus Dibayar</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control bg-light" id="total_amount" value="<?= number_format($invoice['jumlah_tagihan'], 0, ',', '.') ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="print_invoice" value="yes" id="printCheck" checked>
                                    <label class="form-check-label" for="printCheck">
                                        Cetak invoice setelah pembayaran
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <a href="data_tagihan.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check"></i> Konfirmasi Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const originalAmount = <?= $invoice['jumlah_tagihan'] ?>;
    
    function updateTotal() {
        const diskon = parseInt($('#diskon').val()) || 0;
        const total = originalAmount - diskon;
        $('#total_amount').val(total.toLocaleString('id-ID'));
    }
    
    $('#diskon').on('input', function() {
        const diskon = parseInt($(this).val()) || 0;
        const maxDiskon = originalAmount;
        
        if (diskon < 0 || diskon > maxDiskon) {
            $(this).addClass('is-invalid');
            $(this).next('.invalid-feedback').text('Diskon harus antara 0 sampai ' + maxDiskon.toLocaleString('id-ID')).show();
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').hide();
            updateTotal();
        }
    });
    
    $('#paymentForm').on('submit', function(e) {
        let isValid = true;
        
        // Reset all validation
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').hide();
        
        // Validate required fields
        const metodePembayaran = $('[name="metode_pembayaran"]').val();
        const tanggalBayar = $('[name="tanggal_bayar"]').val();
        const diskon = parseInt($('#diskon').val()) || 0;
        
        if (!metodePembayaran) {
            $('[name="metode_pembayaran"]').addClass('is-invalid');
            $('[name="metode_pembayaran"]').next('.invalid-feedback').show();
            isValid = false;
        }
        
        if (!tanggalBayar) {
            $('[name="tanggal_bayar"]').addClass('is-invalid');
            $('[name="tanggal_bayar"]').next('.invalid-feedback').show();
            isValid = false;
        }
        
        if (diskon < 0 || diskon > originalAmount) {
            $('#diskon').addClass('is-invalid');
            $('#diskon').next('.invalid-feedback').show();
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        const totalAmount = originalAmount - diskon;
        const confirmMessage = `Konfirmasi pembayaran sebesar Rp ${totalAmount.toLocaleString('id-ID')} untuk tagihan ${$('#paymentForm input[name="invoice_id"]').val() || '<?= $invoice['id_tagihan'] ?>'}?\n\nPembayaran akan dicatat di mutasi keuangan.`;
        
        if (!confirm(confirmMessage)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>

<?php 
// Include footer
require_once __DIR__ . '/../templates/footer.php';

// End output buffering and flush
ob_end_flush();
?>