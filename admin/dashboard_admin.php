<?php
session_start();
require "../include/db.php";

// Cegah user non-admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Ambil statistik untuk dashboard
$total_tours = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tour"))['total'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders"))['total'];
$total_contacts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM contact"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE Status='Pending'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - JourneyMerapi</title>
    <link rel="stylesheet" href="../Css/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ==================== RESET & GLOBAL ==================== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: #f5f6fa;
    min-width: 1200px;
    overflow-x: auto;
}

/* ==================== SIDEBAR ==================== */
.sidebar {
    width: 250px;
    height: 100vh;
    background: #2c3e50;
    color: white;
    padding: 25px;
    position: fixed;
    top: 0;
    left: 0;
    overflow-y: auto;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar .logo {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 30px;
    text-align: center;
}

.sidebar .logo span {
    color: #f0393d;
}

.sidebar .menu {
    list-style: none;
    margin-top: 20px;
}

.sidebar .menu li {
    margin: 12px 0;
}

.sidebar .menu a {
    text-decoration: none;
    color: #ecf0f1;
    font-size: 16px;
    padding: 12px 15px;
    display: block;
    border-radius: 8px;
    transition: all 0.3s;
}

.sidebar .menu a:hover {
    background: #34495e;
    color: white;
    transform: translateX(5px);
}

.sidebar .menu a.active {
    background: #f0393d;
    color: white;
}

.logout-btn {
    display: block;
    margin-top: 40px;
    background: #e74c3c;
    padding: 12px 15px;
    border-radius: 8px;
    text-align: center;
    text-decoration: none;
    color: white;
    font-weight: 600;
    transition: 0.3s;
}

.logout-btn:hover { 
    background: #c0392b;
    transform: translateY(-2px);
}

/* ==================== MAIN CONTENT ==================== */
.content {
    margin-left: 280px;
    padding: 40px 60px;
    min-width: 900px;
}

h1 {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
}

.welcome-text {
    color: #7f8c8d;
    font-size: 16px;
    margin-bottom: 40px;
}

.welcome-text b {
    color: #f0393d;
}

/* ==================== STATISTICS CARDS ==================== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s;
    border-left: 4px solid;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-card.tours { border-left-color: #3498db; }
.stat-card.orders { border-left-color: #f39c12; }
.stat-card.contacts { border-left-color: #9b59b6; }
.stat-card.users { border-left-color: #2ecc71; }

.stat-card .icon {
    font-size: 36px;
    margin-bottom: 10px;
}

.stat-card .number {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
}

.stat-card .label {
    font-size: 14px;
    color: #7f8c8d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ==================== MENU CARDS ==================== */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s;
}

.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.card h3 {
    font-size: 22px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 12px;
}

.card p {
    color: #7f8c8d;
    font-size: 14px;
    margin-bottom: 20px;
    line-height: 1.6;
}

.btn {
    display: inline-block;
    padding: 12px 28px;
    background: linear-gradient(135deg, #f0393d 0%, #d6282c 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
    text-align: center;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(240, 57, 61, 0.3);
}

/* ==================== RESPONSIVE ==================== */
@media screen and (max-width: 1200px) {
    body {
        min-width: 1200px;
    }
}
</style>

</head>
<body>

<!-- ==================== SIDEBAR ==================== -->
<aside class="sidebar">
    <h2 class="logo">Admin <span>Panel</span></h2>

    <ul class="menu">
        <li><a href="dashboard_admin.php" class="active">📊 Dashboard</a></li>
        <li><a href="kelola_status_merapi.php">🌋 Status Merapi</a></li>
        <li><a href="kelola_tour.php">🗺️ Tour</a></li>
        <li><a href="kelola_order.php">📋 Orders</a></li>
        <li><a href="kelola_contact.php">📩 Contact</a></li>
        <li><a href="kelola_user.php">👤 Users</a></li>
    </ul>

    <a href="../auth/logout.php" class="logout-btn">Logout</a>
</aside>

<!-- ==================== MAIN CONTENT ==================== -->
<main class="content">
    <h1>Dashboard Admin</h1>
    <p class="welcome-text">Selamat datang, <b><?= htmlspecialchars($_SESSION['username']); ?></b> 👋</p>


    <!-- Menu Cards -->
    <div class="cards-grid">
        <div class="card">
            <h3>Status Merapi</h3>
            <p>Kelola dan update status terbaru aktivitas Gunung Merapi untuk keamanan wisatawan.</p>
            <a href="kelola_status_merapi.php" class="btn">Kelola</a>
        </div>

        <div class="card">
            <h3> Data Tour</h3>
            <p>Tambah, edit, atau hapus destinasi wisata yang tersedia di platform.</p>
            <a href="kelola_tour.php" class="btn">Kelola</a>
        </div>

        <div class="card">
            <h3>Pemesanan</h3>
            <p>Lihat dan kelola daftar pemesanan wisata dari pengunjung. <?php if($pending_orders > 0): ?><b style="color:#f39c12;">(<?= $pending_orders ?> pending)</b><?php endif; ?></p>
            <a href="kelola_order.php" class="btn">Kelola</a>
        </div>

        <div class="card">
            <h3>Pesan Masuk</h3>
            <p>Lihat dan tanggapi pesan dari pengunjung melalui halaman kontak.</p>
            <a href="kelola_contact.php" class="btn">Kelola</a>
        </div>

        <div class="card">
            <h3>Users</h3>
            <p>Kelola akun pengguna dan administrator sistem.</p>
            <a href="kelola_user.php" class="btn">Kelola</a>
        </div>
    </div>

</main>
<script src="../responsive.js"></script>
</body>
</html>