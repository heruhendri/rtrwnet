<!-- Sidebar -->
<aside class="sidebar" id="sidebar">

    <!-- User Profile Section -->
    <div class="user-profile-section" style="padding: 20px; text-align: center; border-bottom: 1px solid #425668; margin-bottom: 10px;">
        <div class="user-avatar" style="width: 60px; height: 60px; border-radius: 50%; margin: 0 auto 10px; overflow: hidden; border: 2px solid #1ABB9C;">
            <?php
            // Cek dan convert logo ke base64 untuk bypass .htaccess
            // Selalu reload file untuk memastikan gambar terbaru
            $logo_base64 = '';
            $has_logo = false;
            
            $logo_paths = [
                '../img/logo.png',
                './img/logo.png',
                'img/logo.png',
                '../login.png',
                './login.png',
                'login.png'
            ];
            
            foreach ($logo_paths as $path) {
                if (file_exists($path) && is_readable($path)) {
                    // Selalu baca ulang file tanpa cache
                    clearstatcache(true, $path); // Clear file stat cache
                    $image_data = file_get_contents($path);
                    if ($image_data !== false) {
                        $logo_base64 = 'data:image/png;base64,' . base64_encode($image_data);
                        $has_logo = true;
                        break;
                    }
                }
            }
            ?>
            
            <?php if ($has_logo): ?>
                <img src="<?= $logo_base64 ?>" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <!-- Fallback: Coba akses no-image.png dengan base64 juga -->
                <?php
                $no_image_base64 = '';
                $no_image_paths = [
                    '../img/no-image.png',
                    './img/no-image.png',
                    'img/no-image.png'
                ];
                
                foreach ($no_image_paths as $path) {
                    if (file_exists($path) && is_readable($path)) {
                        $image_data = file_get_contents($path);
                        if ($image_data !== false) {
                            $no_image_base64 = 'data:image/png;base64,' . base64_encode($image_data);
                            break;
                        }
                    }
                }
                ?>
                
                <?php if (!empty($no_image_base64)): ?>
                    <img src="<?= $no_image_base64 ?>" alt="No Image" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <!-- Ultimate fallback: CSS icon -->
                    <div style="width: 100%; height: 100%; background: #1ABB9C; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user" style="color: white; font-size: 24px;"></i>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="user-info">
            <h6 style="color: white; margin: 0; font-size: 14px; font-weight: 500;"><?= htmlspecialchars($current_user['nama_lengkap'] ?? 'Guest') ?></h6>
            <small style="color: #B0BEC5; font-size: 12px;"><?= htmlspecialchars($current_user['level'] ?? '') ?></small>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="../admin/dashboard.php" class="active">
                <i class="fas fa-home menu-icon"></i>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>
        <li class="has-submenu">
            <a href="#" class="menu-toggle">
                <i class="fas fa-users menu-icon"></i>
                <span class="menu-text">Pelanggan</span>
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="../pelanggan/data_pelanggan.php">Data Pelanggan</a></li>
                <li><a href="../pelanggan/tambah_pelanggan.php">Tambah Pelanggan</a></li>
                <li><a href="../pelanggan/active_pelanggan.php">Pelanggan Active</a></li>
            </ul>
        </li>
        <li class="has-submenu">
            <a href="#" class="menu-toggle">
                <i class="fas fa-wifi menu-icon"></i>
                <span class="menu-text">Hotspot</span>
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="../hotspot/list_profile.php">Profile Hotspot</a></li>
                <li><a href="../hotspot/add_profile.php">Tambah Profile</a></li>
                <li><a href="../hotspot/voucher_list.php">List Voucher</a></li>
                <li><a href="../hotspot/generate_voucher.php">Generate Voucher</a></li>
                <li><a href="../hotspot/active_voucher.php">Active Voucher</a></li>
            </ul>
        </li>
        <li class="has-submenu">
            <a href="#" class="menu-toggle">
                <i class="fas fa-globe menu-icon"></i>
                <span class="menu-text">Paket Internet</span>
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="../paket/list_paket_pppoe.php">Profile PPPoE</a></li>
                <li><a href="../paket/tambah_paket_pppoe.php">Tambah Profile</a></li>
            </ul>
        </li>
        <li>
            <a href="../tagihan/data_tagihan.php">
                <i class="fas fa-file-invoice-dollar menu-icon"></i>
                <span class="menu-text">Tagihan</span>
            </a>
        </li>
		<li>
            <a href="../finance/mutasi_keuangan.php">
                <i class="fas fa-file-invoice-dollar menu-icon"></i>
                <span class="menu-text">Riwayat Transaksi</span>
            </a>
        </li>
		<li class="has-submenu">
            <a href="#" class="menu-toggle">
                <i class="fas fa-globe menu-icon"></i>
                <span class="menu-text">FTTH</span>
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="../ftth/data_pop.php">Point of Presence (POP)</a></li>
                <li><a href="../ftth/data_olt.php">Optical Line Terminal (OLT)</a></li>
                <li><a href="../ftth/data_odc.php">Optical Distribution Cabinet (ODC)</a></li>
                <li><a href="../ftth/data_odp.php">Optical Distribution Point (ODP)</a></li>
            </ul>
        </li>
        <li class="has-submenu">
            <a href="#" class="menu-toggle">
                <i class="fas fa-cogs menu-icon"></i>
                <span class="menu-text">Pengaturan</span>
                <i class="fas fa-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="../settings/perusahaan.php">Data Perusahaan</a></li>
                <li><a href="../settings/mikrotik.php">Mikrotik API</a></li>
            </ul>
        </li>
    </ul>

    <!-- Sidebar Footer - Hidden when collapsed -->
    <div class="sidebar-footer">
        <a href="../settings/perusahaan.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Settings">
            <i class="fas fa-cog"></i>
        </a>
        <a href="../settings/mikrotik.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Mikrotik Settings">
            <i class="fas fa-expand-arrows-alt"></i>
        </a>
        <a href="../pelanggan/data_pelanggan.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Customers">
            <i class="fas fa-lock"></i>
        </a>
        <a href="../logout.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</aside>