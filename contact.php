<?php
session_start();

//CEK STATUS LOGIN DI PHP
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>JourneyMerapi - Kontak Merapi</title>

    <link rel="stylesheet" href="Css/style.css" />
    <link rel="stylesheet" href="Css/darkmode.css" />
    <link rel="stylesheet" href="Css/navbar.css" />
    <link rel="stylesheet" href="Css/animations.css" />
    <link rel="stylesheet" href="Css/modal.css" />
    <link rel="stylesheet" href="Css/contact.css" />
    <link rel="stylesheet" href="Css/responsive.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo-about"><span>Journey</span><b>Merapi</b></div>

        <ul class="menu-about">
            <li><a href="index.php">Beranda</a></li>
            <li><a href="about.php">Tentang</a></li>
            <li><a href="tour.php">Tour</a></li>
            <li><a href="order.php">Order Now</a></li>
            <li><a href="contact.php" class="active">Kontak</a></li>
            <li><a href="status.php">Status Merapi</a></li>
        </ul>

        <div class="nav-btns">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span id="userName">Halo, <?= $_SESSION['username'] ?></span>
                <a href="auth/logout.php" class="btn logout">Logout</a>
            <?php else: ?>
                <button id="openLogin" class="btn login">Login</button>
            <?php endif; ?>

            <button id="darkModeToggle" class="btn dark">Dark Mode</button>
        </div>
    </nav>
</header>

<section class="kontak-section"> 
    <h1>Kontak <span>Kami:</span></h1>
    <p>
        Jika Anda ingin bertanya seputar informasi Gunung Merapi atau ingin memberikan kritik dan saran, 
        silakan hubungi kami melalui formulir di bawah ini.
    </p>

    <div class="contact-card">
        <form class="contact-form" id="contactForm">
            <label for="name">Nama:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="contactEmail" name="email" required>

            <label for="message">Pesan:</label>
            <textarea id="message" name="message" rows="4" required></textarea>

            <button type="submit">Kirim</button>
        </form>
    </div>
</section>

<?php include "include/modal.php"; ?>

<footer class="footer-merapi">
    <div class="footer-container">
        <div class="footer-column">
            <h3>JourneyMerapi</h3>
            <p>
                Media informasi dan wisata Gunung Merapi.<br>
                Menyajikan edukasi, sejarah, pesona alam dan pemesanan tiket wisata Merapi.
            </p>
        </div>

        <div class="footer-column">
            <h3>Kontak</h3>
            <p><a href="mailto:jrnymerapi.id@gmail.com">jrnymerapi.id@gmail.com</a></p>
            <p>+62 888 0490 1667</p>
            <p class="copyright">&copy; 2025 JourneyMerapi. All rights reserved.</p>
        </div>

        <div class="footer-column">
            <h3>Alamat</h3>
            <p>Dusun 2, Suroteleng, Kec. Selo, Kab. Boyolali, Jawa Tengah</p>
        </div>
    </div>
</footer>

<script src="auth.js"></script>

<script>
// KIRIM STATUS LOGIN DARI PHP KE JAVASCRIPT
const isUserLoggedIn = <?= json_encode($isLoggedIn) ?>;

// FUNGSI NOTIFIKASI STYLED
function showStyledNotification(message, type = 'warning') {
    const colors = {
        warning: { bg: 'linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%)', shadow: 'rgba(255, 107, 53, 0.4)' },
        success: { bg: 'linear-gradient(135deg, #4CAF50 0%, #45a049 100%)', shadow: 'rgba(76, 175, 80, 0.4)' },
        info: { bg: 'linear-gradient(135deg, #2196F3 0%, #1976D2 100%)', shadow: 'rgba(33, 150, 243, 0.4)' }
    };
    
    const color = colors[type] || colors.warning;
    
    if (!document.querySelector('style[data-styled-notification]')) {
        const style = document.createElement('style');
        style.setAttribute('data-styled-notification', 'true');
        style.textContent = `
            @keyframes notifSlideDown {
                from { transform: translateX(-50%) translateY(-120px) scale(0.8); opacity: 0; }
                to { transform: translateX(-50%) translateY(0) scale(1); opacity: 1; }
            }
            @keyframes notifSlideUp {
                from { transform: translateX(-50%) translateY(0) scale(1); opacity: 1; }
                to { transform: translateX(-50%) translateY(-120px) scale(0.8); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
    
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed; top: 80px; left: 50%; transform: translateX(-50%);
        background: ${color.bg}; color: white; padding: 16px 32px; border-radius: 12px;
        box-shadow: 0 8px 24px ${color.shadow}; z-index: 9999;
        font-family: 'Poppins', sans-serif; font-size: 15px; font-weight: 600;
        text-align: center; min-width: 320px; max-width: 600px;
        animation: notifSlideDown 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        border: 2px solid rgba(255, 255, 255, 0.3);
    `;
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                ${type === 'success' ? 
                    '<polyline points="20 6 9 17 4 12"></polyline>' :
                    '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>'
                }
            </svg>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'notifSlideUp 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        setTimeout(() => notification.remove(), 400);
    }, type === 'success' ? 5000 : 4000);
}

// =============== HANDLE CONTACT FORM SUBMIT ===============
document.getElementById("contactForm").addEventListener("submit", function(e) {
    e.preventDefault();

    // CEK LOGIN MENGGUNAKAN VARIABEL JAVASCRIPT DARI PHP
    if (!isUserLoggedIn) {
        // Tampilkan notifikasi styled
        showStyledNotification('Silakan login terlebih dahulu untuk mengirim pesan.', 'warning');
        
        setTimeout(() => {
            const loginModal = document.getElementById('loginModal');
            if (loginModal) {
                loginModal.classList.add('active');
            }
        }, 500);
        return;
    }

    const name = document.getElementById("name").value;
    const email = document.getElementById("contactEmail").value;
    const message = document.getElementById("message").value;

    fetch("contact_process.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            name: name,
            email: email,
            message: message
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            showStyledNotification(data.message, 'success');
            document.getElementById("contactForm").reset();
        } else {
            showStyledNotification(data.message, 'warning');
        }
    })
    .catch(err => {
        console.error(err);
        showStyledNotification('Terjadi kesalahan saat mengirim pesan.', 'warning');
    });
});
</script>
<script src="responsive.js"></script>
</body>
</html>