<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>JourneyMerapi</title>

    <!-- CSS -->
    <link rel="stylesheet" href="Css/style.css" />
    <link rel="stylesheet" href="Css/navbar.css" />
    <link rel="stylesheet" href="Css/modal.css" />
    <link rel="stylesheet" href="Css/animations.css" />
    <link rel="stylesheet" href="Css/darkmode.css" />
    <link rel="stylesheet" href="Css/responsive.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<!-- ======================= NAVBAR ======================= -->
<header>
    <nav class="navbar">
        <div class="logo"><span>Journey</span><b>Merapi</b></div>

        <ul class="menu">
            <li><a href="index.php" class="active">Beranda</a></li>
            <li><a href="about.php">Tentang</a></li>
            <li><a href="tour.php">Tour</a></li>
            <li><a href="order.php">Order Now</a></li>
            <li><a href="contact.php">Kontak</a></li>
            <li><a href="status.php">Status Merapi</a></li>
        </ul>

        <div class="nav-btns">

            <!-- Jika belum login -->
            <?php if (!isset($_SESSION['user_id'])): ?>
                <button id="openLogin" class="btn login">Login</button>
            <?php endif; ?>

            <!-- Jika sudah login -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-text">Halo, <?= $_SESSION['username']; ?></span>
                <a href="auth/logout.php" class="btn logout">Logout</a>
            <?php endif; ?>

            <button id="darkModeToggle" class="btn dark">Dark Mode</button>
        </div>
    </nav>
</header>


<!-- ======================= HERO SECTION ======================= -->
<div class="main-content-bg">

    <section id="home" class="hero">
        <div class="hero-content">
            <p class="small-text fade-up">TRIP TO MERAPI MOUNTAIN____</p>

            <h1 class="title fade-up-delay">
                The Majestic of <br><span>MERAPI</span>
            </h1>

            <p class="desc fade-up-delay2">
                Kami hadir membawamu berkenalan dengan Gunung Merapi secara mendalam. 
                Melihat keindahan alamnya serta mendapatkan informasi penting yang akan 
                menambah pengetahuanmu.
            </p>

            <a href="about.php" class="btn action fade-up-delay3">About</a>
        </div>
    </section>

    <!-- ======================= FAKTA MENARIK ======================= -->
    <section class="facta-section"> 
        <h2>Fakta Menarik <span>Merapi</span></h2>

        <div class="card-container">

            <div class="card reveal">
                <img src="https://hypeabis.id/assets/photo/20230903041142000000169368891252001.jpeg">
                <h3>Penuh dengan Mitos</h3>
                <p>
                    Desa-desa dekat Merapi percaya bahwa salah satu istana kerajaan roh terletak di Merapi.
                </p>
            </div>

            <div class="card reveal">
                <img src="img/kaliurang.jpg">
                <h3>Kawasan Wisata</h3>
                <p>
                    Kawasan sekitar Merapi seperti Kaliurang menjadi destinasi wisata populer.
                </p>
            </div>

            <div class="card reveal">
                <img src="https://lh3.googleusercontent.com/p/AF1QipPpF3vYpNAjBAqre9Hc99bWzoJgXwiODEnS7zKT">
                <h3>Museum Gunung Merapi</h3>
                <p>
                    Tempat edukatif untuk mengenal sejarah letusan dan fenomena Merapi.
                </p>
            </div>

        </div>
    </section>

</div>


<!-- ======================= LOGIN & REGISTER MODAL ======================= -->
<?php include "include/modal.php"; ?>


<!-- ======================= FOOTER ======================= -->
<footer>
    © 2025 JourneyMerapi. All rights reserved.
</footer>


<!-- ======================= JAVASCRIPT ======================= -->
<script src="auth.js"></script>

<!-- Reveal Animation -->
<script>
document.addEventListener("scroll", function() {
    document.querySelectorAll('.reveal').forEach(el => {
        const top = el.getBoundingClientRect().top;
        if (top < window.innerHeight - 80) {
            el.classList.add('show');
        }
    });
});
</script>

<!-- Navbar Scroll Effect -->
<script>
window.addEventListener("scroll", () => {
    const navbar = document.querySelector(".navbar");
    navbar.classList.toggle("scrolled", window.scrollY > 40);
});
</script>

<!-- AUTO OPEN LOGIN MODAL (setelah registrasi) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('action') === 'openLogin') {
        const loginModal = document.getElementById('loginModal');
        if (loginModal) {
            setTimeout(() => {
                loginModal.classList.add('active');
            }, 500);
        }
        
        // Hapus parameter dari URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>
<script src="responsive.js"></script>
</body>
</html>
