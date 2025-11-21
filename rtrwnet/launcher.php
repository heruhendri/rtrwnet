<?php
// [Previous PHP code remains exactly the same until the HTML part]
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RTRWNet Billing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #F7F7F7;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .launcher-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .launcher-card {
            background: #FFFFFF;
            border: 1px solid #E6E9ED;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 40px;
            width: 100%;
            max-width: 480px;
            text-align: center;
            margin: auto;
        }
        .launcher-logo {
            font-size: 3.5rem;
            color: #2A3F54;
            margin-bottom: 20px;
        }
        .progress-container {
            margin: 25px 0;
        }
        .progress {
            height: 6px;
            border-radius: 0;
        }
        .progress-bar {
            background-color: #2A3F54;
        }
        .status-text {
            font-size: 1rem;
            margin: 20px 0;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #73879C;
            line-height: 1.5;
        }
        .spinner {
            margin-right: 10px;
        }
        .btn-launch {
            background-color: #2A3F54;
            border: none;
            border-radius: 0;
            color: white;
            padding: 10px 30px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.2s;
            width: 100%;
            max-width: 220px;
            margin: 10px auto;
        }
        .btn-launch:hover {
            background-color: #1E2E3B;
            color: white;
        }
        .version-info {
            margin-top: 25px;
            padding: 15px;
            background: #F5F7FA;
            border: 1px solid #E6E9ED;
            font-size: 0.85rem;
            color: #73879C;
            text-align: left;
        }
        h2 {
            color: #2A3F54;
            font-size: 1.6rem;
            font-weight: 500;
            margin-bottom: 10px;
        }
        .text-muted {
            color: #73879C !important;
            margin-bottom: 25px;
        }
        .fa-check-circle {
            color: #26B99A;
        }
        .fa-exclamation-triangle {
            color: #E74C3C;
        }
        .fa-wifi {
            color: #3498DB;
        }
    </style>
</head>
<body>
    <div class="launcher-container">
        <div class="launcher-card">
            <div class="launcher-logo">
                <i class="fas fa-network-wired"></i>
            </div>
            <h2>RTRWNet Billing System</h2>
            <p class="text-muted">Memulai aplikasi dan memeriksa update...</p>
            
            <div class="progress-container">
                <div class="progress" style="display: none;" id="progress-bar">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 0%"></div>
                </div>
            </div>
            
            <div class="status-text" id="status-text">
                <div class="spinner-border spinner-border-sm spinner" role="status"></div>
                Memeriksa update...
            </div>
            
            <button class="btn btn-launch" id="launch-btn" onclick="launchApp()" style="display: none;">
                <i class="fas fa-sign-in-alt me-2"></i>
                Masuk ke Aplikasi
            </button>
            
            <div class="version-info" id="version-info" style="display: none;"></div>
        </div>
    </div>

    <script>
        // [JavaScript code remains exactly the same]
        function updateStatus(message, showSpinner = true) {
            const statusEl = document.getElementById('status-text');
            statusEl.innerHTML = showSpinner 
                ? `<div class="spinner-border spinner-border-sm spinner" role="status"></div>${message}`
                : message;
        }
        
        function updateProgress(percent) {
            const progressBar = document.getElementById('progress-bar');
            const progressFill = progressBar.querySelector('.progress-bar');
            
            if (percent > 0) {
                progressBar.style.display = 'block';
                progressFill.style.width = percent + '%';
            } else {
                progressBar.style.display = 'none';
            }
        }
        
        function showLaunchButton(versionInfo = '') {
            document.getElementById('launch-btn').style.display = 'inline-block';
            if (versionInfo) {
                const versionEl = document.getElementById('version-info');
                versionEl.innerHTML = versionInfo;
                versionEl.style.display = 'block';
            }
        }
        
        async function checkForUpdates() {
            try {
                const response = await fetch('launcher.php?action=check');
                const data = await response.json();
                
                const versionInfo = `<strong>Versi Lokal:</strong> ${data.local_version}<br><strong>Versi Server:</strong> ${data.server_version || 'Tidak tersedia'}`;
                
                if (data.update_available) {
                    updateStatus('Update tersedia! Mendownload file...', true);
                    updateProgress(10);
                    
                    setTimeout(() => {
                        performPatch();
                    }, 1000);
                } else {
                    updateStatus('<i class="fas fa-check-circle me-2"></i>Aplikasi sudah versi terbaru', false);
                    showLaunchButton(versionInfo);
                }
            } catch (error) {
                console.error('Error checking updates:', error);
                updateStatus('<i class="fas fa-wifi me-2"></i>Tidak dapat memeriksa update, melanjutkan...', false);
                showLaunchButton('<strong>Status:</strong> Offline Mode');
            }
        }
        
        async function performPatch() {
            try {
                updateProgress(30);
                updateStatus('Mendownload file langsung dari server...', true);
                
                const response = await fetch('launcher.php?action=patch');
                const result = await response.json();
                
                updateProgress(90);
                
                if (result.status === 'success') {
                    updateStatus('<i class="fas fa-check-circle me-2"></i>Update berhasil! ' + result.message, false);
                    updateProgress(100);
                    
                    setTimeout(() => {
                        showLaunchButton(`<strong>Update berhasil!</strong><br>File diupdate: ${result.updated_files}/${result.total_files}`);
                    }, 1000);
                } else {
                    updateStatus('<i class="fas fa-exclamation-triangle me-2"></i>' + result.message, false);
                    showLaunchButton('<strong>Status:</strong> Update gagal, melanjutkan dengan versi lama');
                }
            } catch (error) {
                console.error('Error during patch:', error);
                updateStatus('<i class="fas fa-exclamation-triangle me-2"></i>Gagal melakukan update', false);
                showLaunchButton('<strong>Status:</strong> Update gagal, melanjutkan dengan versi lama');
            }
        }
        
        function launchApp() {
            updateStatus('Membuka aplikasi...', true);
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 500);
        }
        
        // Start the launcher
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(checkForUpdates, 1000);
        });
    </script>
</body>
</html>