<?php
session_start();
require "include/db.php"; // koneksi database
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour - JourneyMerapi</title>

    <!-- CSS -->
    <link rel="stylesheet" href="Css/style.css">
    <link rel="stylesheet" href="Css/navbar.css">
    <link rel="stylesheet" href="Css/tour.css">
    <link rel="stylesheet" href="Css/modal.css">
    <link rel="stylesheet" href="Css/darkmode.css">
    <link rel="stylesheet" href="Css/animations.css">
    <link rel="stylesheet" href="Css/responsive.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<header>
    <nav class="navbar">
        <div class="logo"><span>Journey</span><b>Merapi</b></div>

        <ul class="menu">
            <li><a href="index.php">Beranda</a></li>
            <li><a href="about.php">Tentang</a></li>
            <li><a href="tour.php" class="active">Tour</a></li>
            <li><a href="order.php">Order Now</a></li>
            <li><a href="contact.php">Kontak</a></li>
            <li><a href="status.php">Status Merapi</a></li>
        </ul>

        <div class="nav-btns">

            <?php if (!isset($_SESSION['user_id'])): ?>
                <button id="openLogin" class="btn login">Login</button>
            <?php else: ?>
                <span id="userName">Halo, <?= $_SESSION['username']; ?></span>
                <a href="auth/logout.php" class="btn logout">Logout</a>
            <?php endif; ?>

            <button id="darkModeToggle" class="btn dark">Dark Mode</button>
        </div>
    </nav>
</header>


<div class="main-tour-bg">
    <section class="hero-tour">
        <h2>Pilih <span>Destinasi Wisata</span> Terbaikmu</h2>
    </section>

    <section class="card-container-tour">

        <?php
        // ===============================
        // BACA DATA TOUR DARI DATABASE
        // ===============================

        $query = mysqli_query($conn, "SELECT * FROM tour ORDER BY ID_Tour ASC");

        if (mysqli_num_rows($query) === 0):
        ?>
            <p style="color:white; text-align:center; width:100%; margin-top:50px;">
                Belum ada destinasi wisata yang tersedia.
            </p>
        <?php
        endif;

        while($t = mysqli_fetch_assoc($query)):
        ?>

        <div class="card-tour">
            <img src="<?= $t['Gambar'] ?>" alt="<?= $t['Nama'] ?>">
            <h3><?= $t['Nama'] ?></h3>

            <p>Mulai Dari</p>
            <span class="price">IDR <?= number_format($t['Harga_mulai'], 0, ',', '.') ?> / Orang</span>

            <div class="info">
                <p>⭐ Rating: <?= $t['Rating'] ?>/5</p>
            </div>

            <!-- Kirim ID_Tour ke halaman order -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- User sudah login, langsung redirect -->
                <a href="order.php?tour=<?= $t['ID_Tour'] ?>" class="btn pesan">Pesan</a>
            <?php else: ?>
                <!-- User belum login, tampilkan modal -->
                <button class="btn pesan" 
                        data-tour-id="<?= $t['ID_Tour'] ?>"
                        data-tour-name="<?= htmlspecialchars($t['Nama'], ENT_QUOTES) ?>">
                    Pesan
                </button>
            <?php endif; ?>
        </div>

        <?php endwhile; ?>

    </section>
</div>


<!-- LOGIN / REGISTER MODAL -->
<?php include "include/modal.php"; ?>

<footer>
    © 2025 JourneyMerapi. All rights reserved.
</footer>

<!-- LOAD auth.js HANYA SEKALI - DI AKHIR BODY -->
<script src="auth.js"></script>

<!-- SCRIPT UNTUK HANDLE TOMBOL PESAN -->
<script>
function initTourButtons() {
    // Debug: Cek apakah fungsi sudah tersedia
    console.log('Checking functions...');
    console.log('handleTourOrder available:', typeof window.handleTourOrder);
    console.log('showLoginRequiredNotification available:', typeof window.showLoginRequiredNotification);
    
    // HANDLE KLIK TOMBOL PESAN
    document.querySelectorAll('.btn.pesan[data-tour-id]').forEach(button => {
        button.addEventListener('click', function() {
            const tourId = this.getAttribute('data-tour-id');
            const tourName = this.getAttribute('data-tour-name');
            
            console.log('Button clicked:', tourId, tourName);
            
            // Cek apakah fungsi tersedia
            if (typeof window.handleTourOrder === 'function') {
                window.handleTourOrder(tourId, tourName);
            } else {
                console.warn('handleTourOrder not available, using fallback');
        
                // Simpan tour ke sessionStorage
                sessionStorage.setItem('pendingTourOrder', JSON.stringify({
                    id: tourId,
                    name: tourName,
                    timestamp: Date.now()
                }));
                
                // Tampilkan notifikasi manual
                showManualNotification('Silakan login terlebih dahulu untuk memesan tour.');
                
                // Buka modal login
                setTimeout(() => {
                    const loginModal = document.getElementById('loginModal');
                    if (loginModal) {
                        loginModal.classList.add('active');
                        console.log('Login modal opened (fallback)');
                    } else {
                        alert('Silakan login terlebih dahulu untuk memesan tour.');
                    }
                }, 600);
            }
        });
    });
}

//FUNGSI NOTIFIKASI MANUAL (jika auth.js gagal load)
function showManualNotification(message) {
    // Add animation styles if not exist
    if (!document.querySelector('style[data-manual-notification]')) {
        const style = document.createElement('style');
        style.setAttribute('data-manual-notification', 'true');
        style.textContent = `
            @keyframes slideDownManual {
                from { 
                    transform: translateX(-50%) translateY(-120px) scale(0.8); 
                    opacity: 0; 
                }
                to { 
                    transform: translateX(-50%) translateY(0) scale(1); 
                    opacity: 1; 
                }
            }
            @keyframes slideUpManual {
                from { 
                    transform: translateX(-50%) translateY(0) scale(1); 
                    opacity: 1; 
                }
                to { 
                    transform: translateX(-50%) translateY(-120px) scale(0.8); 
                    opacity: 0; 
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 80px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
        color: white;
        padding: 16px 32px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(255, 107, 53, 0.4);
        z-index: 9999;
        font-family: 'Poppins', sans-serif;
        font-size: 15px;
        font-weight: 600;
        text-align: center;
        min-width: 320px;
        max-width: 500px;
        animation: slideDownManual 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        border: 2px solid rgba(255, 255, 255, 0.3);
    `;
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideUpManual 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        setTimeout(() => notification.remove(), 400);
    }, 4000);
}

// INIT DENGAN MULTIPLE DELAYS UNTUK MEMASTIKAN AUTH.JS LOADED
window.addEventListener('load', function() {
    // Try setelah 50ms
    setTimeout(initTourButtons, 50);
});

// Backup: jika window.load sudah lewat
if (document.readyState === 'complete') {
    setTimeout(initTourButtons, 50);
} else {
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initTourButtons, 50);
    });
}
</script>
<script src="responsive.js"></script>
</body>
</html>