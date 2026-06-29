<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tentang Merapi - JourneyMerapi</title>

    <!-- CSS -->
    <link rel="stylesheet" href="Css/style.css">
    <link rel="stylesheet" href="Css/about.css">
    <link rel="stylesheet" href="Css/navbar.css">
    <link rel="stylesheet" href="Css/modal.css">
    <link rel="stylesheet" href="Css/animations.css">
    <link rel="stylesheet" href="Css/darkmode.css">
    <link rel="stylesheet" href="Css/responsive.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<!-- ======================= NAVBAR ======================= -->
<header>
    <nav class="navbar">
        <div class="logo-about"><span>Journey</span><b>Merapi</b></div>

        <ul class="menu-about">
            <li><a href="index.php">Beranda</a></li>
            <li><a href="about.php" class="active">Tentang</a></li>
            <li><a href="tour.php">Tour</a></li>
            <li><a href="order.php">Order Now</a></li>
            <li><a href="contact.php">Kontak</a></li>
            <li><a href="status.php">Status Merapi</a></li>
        </ul>

        <div class="nav-btns">

            <?php if (!isset($_SESSION['user_id'])): ?>
                <button id="openLogin" class="btn login">Login</button>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-text">Halo, <?= $_SESSION['username']; ?></span>
                <a href="auth/logout.php" class="btn logout">Logout</a>
            <?php endif; ?>

            <button id="darkModeToggle" class="btn dark">Dark Mode</button>
        </div>
    </nav>
</header>


<!-- ======================= ABOUT SECTION ======================= -->
<section id="tentang" class="about-section">
    <div class="main-row">

        <!-- LEFT TEXT -->
        <div class="left-col">
            <div class="about-block">
                <h2>Tentang <span>MERAPI</span></h2>
                <p class="text">
                    Gunung Merapi merupakan salah satu gunung berapi paling aktif di dunia yang terletak
                    di perbatasan antara Provinsi Jawa Tengah dan Daerah Istimewa Yogyakarta.
                    Dengan ketinggian sekitar 2.930 MDPL, Merapi menjadi ikon alam, budaya, dan sejarah Indonesia.
                </p>
            </div>

            <div class="history-block">
                <h2>Sejarah <span>MERAPI</span></h2>
                <p class="text">
                    Catatan sejarah menunjukkan letusan Merapi terjadi sejak abad ke-10.
                    Letusan besar terjadi pada tahun 1930, 1994, dan 2010.
                    Letusan tahun 2010 merupakan salah satu yang paling dahsyat,
                    mengubah lanskap sekitarnya secara signifikan.
                </p>
            </div>
        </div>

        <!-- RIGHT IMAGE GRID -->
        <div class="right-col">
            <div class="img-grid">
                <img src="img/view6.jpg" alt="Merapi View">
                <img src="img/view2.jpeg" alt="Merapi View">
                <img src="img/view5.jpeg" alt="Merapi View">
                <img src="img/view4.jpg" alt="Merapi View">
            </div>
        </div>

    </div>
</section>


<!-- ======================= DESTINASI WISATA ======================= -->
<section class="wisata-merapi">

    <h1>Destinasi Wisata <span>MERAPI</span></h1>
    <p>
        Selain keindahan alamnya, kawasan Gunung Merapi memiliki beragam destinasi wisata <br>
        yang sarat sejarah, petualangan, serta pengalaman edukatif.
    </p>

    <div class="grid-wisata">

        <?php
        $destinasi = [
            [
                "img" => "https://ik.imagekit.io/pandooin/tr:pr-true/production/images/attraction/lava-tour-merapi/xZGPIyHj1qC45HKdCoXmsV8bLYP1AkSW1KXVIu0I.jpg",
                "nama" => "Jeep Lava Tour Merapi",
                "lokasi" => "Kaliurang, Sleman",
                "rating" => 4.8,
                "desc" => "Petualangan menyusuri jalur bekas letusan Merapi menggunakan jeep off-road."
            ],
            [
                "img" => "img/bungkerkaliadem.jpeg",
                "nama" => "Bunker Kaliadem",
                "lokasi" => "Cangkringan, Sleman",
                "rating" => 4.7,
                "desc" => "Bunker bersejarah dengan pemandangan langsung menuju puncak Merapi."
            ],
            [
                "img" => "img/museumsisahartaku.jpg",
                "nama" => "Museum Sisa Hartaku",
                "lokasi" => "Kepuharjo, Cangkringan",
                "rating" => 4.6,
                "desc" => "Museum peninggalan letusan Merapi 2010 berisi barang-barang warga."
            ],
            [
                "img" => "img/bukit_klangon.jpg",
                "nama" => "Bukit Klangon",
                "lokasi" => "Glagaharjo, Sleman",
                "rating" => 4.8,
                "desc" => "Spot sunrise terbaik untuk melihat Gunung Merapi dari dekat."
            ],
            [
                "img" => "img/Lostworldcastle.jpg",
                "nama" => "The Lost World Castle",
                "lokasi" => "Kaliurang Barat, Sleman",
                "rating" => 4.5,
                "desc" => "Taman wisata bertema kastil abad pertengahan dengan spot foto unik."
            ],
            [
                "img" => "img/merapipark.jpg",
                "nama" => "The World Landmarks Merapi Park",
                "lokasi" => "Hargobinangun, Sleman",
                "rating" => 4.6,
                "desc" => "Miniatur ikon dunia seperti Eiffel, Liberty, dan Big Ben."
            ],
            [
                "img" => "img/museumsentalu.jpg",
                "nama" => "Museum Ullen Sentalu",
                "lokasi" => "Kaliurang, Sleman",
                "rating" => 4.8,
                "desc" => "Museum seni & budaya Jawa yang misterius dengan koleksi bersejarah."
            ],
            [
                "img" => "img/bhumimerapi.jpg",
                "nama" => "Agrowisata Bhumi Merapi",
                "lokasi" => "Kaliurang, Sleman",
                "rating" => 4.7,
                "desc" => "Wisata edukasi hewan & pertanian dengan spot ala Eropa."
            ],
            [
                "img" => "img/embungkaliaji.jpg",
                "nama" => "Embung Kaliaji",
                "lokasi" => "Cangkringan, Sleman",
                "rating" => 4.6,
                "desc" => "Spot tenang untuk menikmati sunset di pedesaan Merapi."
            ]
        ];

        foreach ($destinasi as $d):
        ?>
            <div class="wisata-card">
                <img src="<?= $d['img'] ?>" alt="<?= $d['nama'] ?>">
                <h3><?= $d['nama'] ?></h3>

                <p class="location">📍 <?= $d['lokasi'] ?></p>
                <p class="rating">⭐ <?= $d['rating'] ?>/5.0</p>

                <p><?= $d['desc'] ?></p>
            </div>
        <?php endforeach; ?>

    </div>

</section>


<!-- ======================= MODAL (DARI include/modal.php) ======================= -->
<?php include "include/modal.php"; ?>


<!-- ======================= FOOTER ======================= -->
<footer>
    © 2025 JourneyMerapi. All rights reserved.
</footer>


<!-- ======================= JAVASCRIPT ======================= -->
<script src="auth.js"></script>

<script>
// Reveal animation
document.addEventListener("scroll", function() {
    document.querySelectorAll('.reveal').forEach(el => {
        if (el.getBoundingClientRect().top < window.innerHeight - 80) {
            el.classList.add('show');
        }
    });
});

// Navbar scroll effect
window.addEventListener("scroll", () => {
    const navbar = document.querySelector(".navbar");
    navbar.classList.toggle("scrolled", window.scrollY > 40);
});

// Page transition
document.body.classList.add("page-transition");
window.addEventListener("load", () => {
    setTimeout(() => document.body.classList.add("page-loaded"), 50);
});
</script>
<script src="responsive.js"></script>
</body>
</html>
