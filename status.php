<?php
session_start();
require "include/db.php";

// Ambil status terbaru
$query = mysqli_query($conn, 
    "SELECT sm.*, a.Username 
     FROM status_merapi sm
     LEFT JOIN admin a ON sm.Admin_id = a.ID_admin
     ORDER BY sm.Update_time DESC 
     LIMIT 1");

$status = mysqli_fetch_assoc($query);

$level = $status['Level'] ?? "Belum Ada";
$level_class = strtolower($level);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Gunung Merapi</title>

    <link rel="stylesheet" href="Css/style.css">
    <link rel="stylesheet" href="Css/navbar.css">
    <link rel="stylesheet" href="Css/status.css">
    <link rel="stylesheet" href="Css/darkmode.css"> 
    <link rel="stylesheet" href="Css/responsive.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<!-- ======================= NAVBAR ======================= -->
<header>
    <nav class="navbar">
        <div class="logo"><span>Journey</span><b>Merapi</b></div>

        <ul class="menu-about">
            <li><a href="index.php">Beranda</a></li>
            <li><a href="about.php">Tentang</a></li>
            <li><a href="tour.php">Tour</a></li>
            <li><a href="order.php">Order Now</a></li>
            <li><a href="contact.php">Kontak</a></li>
            <li><a href="status.php" class="active">Status Merapi</a></li>
        </ul>

        <div class="nav-btns">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <button id="openLogin" class="btn login">Login</button>
            <?php else: ?>
                <span id="userName">Halo, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="auth/logout.php" class="btn logout">Logout</a>
            <?php endif; ?>
            <button id="darkModeToggle" class="btn dark">Dark Mode</button>
        </div>
    </nav>
</header>

<!-- ======================= STATUS CONTENT ======================= -->
<div class="status-container">
    <h1>Status Aktivitas Gunung Merapi</h1>

    <?php if (!$status): ?>
        <div class="status-box">
            <div class="level no-data">Belum Ada Data</div>
            <p class="desc">Status aktivitas Merapi belum diperbarui admin.</p>
            <p class="rek">-</p>

            <small>Update terakhir: -</small>
            <small>Oleh Admin: -</small>
        </div>

    <?php else: ?>
        <div class="status-box">
            <div class="level <?= $level_class ?>">
                <?= htmlspecialchars($status['Level']) ?>
            </div>

            <p class="desc"><?= nl2br(htmlspecialchars($status['Deskripsi'])) ?></p>

            <h3>Rekomendasi</h3>
            <p class="rek"><?= nl2br(htmlspecialchars($status['Rekomendasi'])) ?></p>

            <small>Update terakhir: <?= $status['Update_time'] ?></small>
            <small>Oleh Admin: <?= htmlspecialchars($status['Username']) ?></small>
        </div>
    <?php endif; ?>

    <a href="index.php" class="btn-back">Kembali</a>
</div>


<!-- ======================= FOOTER ======================= -->
<footer class="footer-merapi">
    <div class="footer-container">

        <div class="footer-column">
            <h3>JourneyMerapi</h3>
            <p>
                Media informasi dan wisata Gunung Merapi.<br>
                Edukasi, sejarah, pesona alam, & pemesanan wisata.
            </p>
        </div>

        <div class="footer-column">
            <h3>Kontak</h3>
            <p><a href="mailto:jrnymerapi.id@gmail.com">jrnymerapi.id@gmail.com</a></p>
            <p>+62 888 0490 1667</p>
            <p class="copyright">© 2025 JourneyMerapi. All rights reserved.</p>
        </div>

        <div class="footer-column">
            <h3>Alamat</h3>
            <p>Dusun 2, Suroteleng, Selo, Boyolali, Jawa Tengah</p>
        </div>

    </div>
</footer>

<?php include "include/modal.php"; ?>
<script src="auth.js"></script>
<script>
// Dark Mode Toggle Functionality
const darkModeToggle = document.getElementById('darkModeToggle');
const body = document.body;

// Cek preferensi tersimpan
if (localStorage.getItem('darkMode') === 'enabled') {
    body.classList.add('dark-mode');
    darkModeToggle.textContent = 'Light Mode';
}

</script>
<script src="responsive.js"></script>

</body>
</html>
