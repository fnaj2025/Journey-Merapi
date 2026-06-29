<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= isset($pageTitle) ? $pageTitle : "JourneyMerapi"; ?></title>

    <!-- CSS GLOBAL -->
    <link rel="stylesheet" href="Css/navbar.css">
    <link rel="stylesheet" href="Css/style.css">
    <link rel="stylesheet" href="Css/modal.css">
    <link rel="stylesheet" href="Css/darkmode.css">
    <link rel="stylesheet" href="Css/animations.css">
    <link rel="stylesheet" href="Css/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="page-transition">

<header>
    <nav class="navbar">

        <!-- LOGO -->
        <div class="logo">
            <span>Journey</span><b>Merapi</b>
        </div>

        <!-- MENU -->
        <ul class="menu">
            <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Beranda</a></li>
            <li><a href="about.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">Tentang</a></li>
            <li><a href="tour.php" class="<?= basename($_SERVER['PHP_SELF']) == 'tour.php' ? 'active' : '' ?>">Tour</a></li>
            <li><a href="order.php" class="<?= basename($_SERVER['PHP_SELF']) == 'order.php' ? 'active' : '' ?>">Order Now</a></li>
            <li><a href="contact.php" class="<?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>">Kontak</a></li>
            <li><a href="status.php" class="<?= basename($_SERVER['PHP_SELF']) == 'status.php' ? 'active' : '' ?>">Status Merapi</a></li>
        </ul>

        <!-- BUTTON USER -->
        <div class="nav-btns">

            <?php if (isset($_SESSION['user_id'])): ?>
                <span id="userName">Halo, <?= $_SESSION['username']; ?></span>
                <a href="auth/logout.php" class="btn logout">Logout</a>
            <?php else: ?>
                <button id="openLogin" class="btn login">Login</button>
            <?php endif; ?>

            <button id="darkModeToggle" class="btn dark">Dark Mode</button>
        </div>
    </nav>
</header>


<?php include "include/modal.php"; ?>