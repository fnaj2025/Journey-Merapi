<?php
session_start();
require "include/db.php";

if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak! Anda harus login terlebih dahulu.");
}

$ID_User = $_SESSION['user_id'];

// Pastikan ada order_id
if (!isset($_GET['id'])) {
    die("Order tidak ditemukan.");
}

$ID_Order = intval($_GET['id']);

// Ambil order utama
$order = mysqli_query($conn, "
    SELECT o.*, u.Username 
    FROM orders o
    LEFT JOIN users u ON o.ID_User = u.ID_User
    WHERE o.ID_Order = $ID_Order AND o.ID_User = $ID_User
");

if (mysqli_num_rows($order) === 0) {
    die("Order tidak ditemukan atau bukan milik Anda.");
}

$order = mysqli_fetch_assoc($order);

// Ambil destinasi dari order_items
$items = mysqli_query($conn, "SELECT * FROM order_items WHERE ID_Order = $ID_Order");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice Pemesanan - JourneyMerapi</title>
<link rel="stylesheet" href="Css/responsive.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ==================== GLOBAL ==================== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background: #f5f6fa;
    padding: 30px;
}

/* ==================== INVOICE BOX ==================== */
.invoice-box {
    max-width: 800px;
    margin: auto;
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,.1);
}

/* ==================== HEADER ==================== */
h1 {
    text-align: center;
    margin-bottom: 10px;
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
}

p.header {
    text-align: center;
    color: #1f2727ff;
    margin-bottom: 8px;
}

.invoice-box > p:not(.header) {
    text-align: left;
    color: #1f2727ff;
    margin-bottom: 8px;
}

hr {
    border: none;
    border-top: 2px solid #ecf0f1;
    margin: 25px 0;
}

/* ==================== SECTIONS ==================== */
h3 {
    margin-top: 30px;
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 15px;
}

.section-box > p {
    line-height: 1.8;
    color: #2c3e50;
    margin-bottom: 8px;
     text-align: center;
}

.section-box > p strong {
    font-weight: 600;
    color: #34495e;
    display: inline-block;
    min-width: 150px;
     text-align: center;
}

/* ==================== TABLE ==================== */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,.08);
}

.table th {
    text-align: left;
    background: #f0393d;
    color: #fff;
    padding: 14px;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    padding: 14px;
    border-bottom: 1px solid #ecf0f1;
    color: #2c3e50;
    font-size: 14px;
}

.table tr:last-child td {
    border-bottom: none;
}

.table tbody tr:hover {
    background: #f8f9fa;
}

/* ==================== STATUS BOX ==================== */
.status-box {
    padding: 15px;
    margin-top: 20px;
    border-radius: 10px;
    text-align: center;
    font-weight: 600;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.pending { 
    background: #fff3cd; 
    color: #856404;
    border: 2px solid #ffc107;
}

.process { 
    background: #d1ecf1; 
    color: #0c5460;
    border: 2px solid #17a2b8;
}

.done { 
    background: #d4edda; 
    color: #155724;
    border: 2px solid #28a745;
}

/* ==================== BUTTONS ==================== */
.btn-download {
    display: block;
    margin: 30px auto 10px;
    padding: 14px 30px;
    background: linear-gradient(135deg, #f0393d 0%, #d6282c 100%);
    color: white;
    font-weight: 600;
    font-size: 16px;
    border-radius: 10px;
    text-align: center;
    width: 280px;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(240, 57, 61, 0.3);
}

.btn-download:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(240, 57, 61, 0.4);
}

.back-btn {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: #7f8c8d;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}

.back-btn:hover {
    color: #f0393d;
}

/* ==================== ALERT ==================== */
.invoice-box > p[style*="text-align:center; color:#b07a00"] {
    background: #fff3cd;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #ffc107;
    margin-top: 20px;
    color: #856404 !important;
}
</style>

</head>
<body>

<div class="invoice-box">

    <h1>Invoice Pemesanan</h1>
    <p class= "header"> JourneyMerapi • Bukti Reservasi Tour Wisata</p>

    <hr>

    <h3>Informasi Pemesan:</h3>
    <p><strong>Nama:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
    <p><strong>Username:</strong> <?= htmlspecialchars($order['Username']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
    <p><strong>Telepon:</strong> <?= htmlspecialchars($order['phone']) ?></p>
    <p><strong>Negara:</strong> <?= htmlspecialchars($order['country']) ?></p>
    <p><strong>Pickup:</strong> <?= date('d/m/Y', strtotime($order['pickup_date'])) ?> — <?= $order['pickup_time'] ?></p>
    <p><strong>Jumlah Orang:</strong> <?= $order['Jumlah_orang'] ?> orang</p>
    <p><strong>Lokasi Pickup:</strong> <?= htmlspecialchars($order['pickup_location']) ?></p>

    <h3>Detail Destinasi</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Destinasi</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($i = mysqli_fetch_assoc($items)): ?>
            <tr>
                <td><?= htmlspecialchars($i['Tour_Name']) ?></td>
                <td>IDR <?= number_format($i['Price'],0,",",".") ?></td>
                <td><?= $i['Quantity'] ?> pax</td>
                <td><strong>IDR <?= number_format($i['Subtotal'],0,",",".") ?></strong></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

<div class="section-box">
    <h3>Total Pembayaran</h3>
    <p style="font-size:24px; font-weight:700; color:#f0393d;">
        IDR <?= number_format($order['total_price'],0,",",".") ?>
    </p>

    <h3>Status Pemesanan</h3>
    <?php 
        $status = $order['Status'];
        $class = ($status == "Pending" ? "pending" : ($status == "Diproses" ? "process" : "done"));
    ?>
    <div class="status-box <?= $class ?>">
        <?php if($status == "Pending"): ?>
            ⏳ <?= $status ?>
        <?php elseif($status == "Diproses"): ?>
            🔄 <?= $status ?>
        <?php else: ?>
            ✅ <?= $status ?>
        <?php endif; ?>
    </div>

    <?php if ($status === "Selesai" || $status === "Diproses"): ?>
        <a href="invoice_pdf.php?id=<?= $ID_Order ?>" class="btn-download">📄 Download Invoice (PDF)</a>
    <?php else: ?>
        <p style="text-align:center; color:#b07a00;">
            *Menunggu konfirmasi admin untuk mengaktifkan cetak invoice.
        </p>
    <?php endif; ?>

    <a href="my_order.php" class="back-btn">← Kembali ke halaman riwayat pemesanan</a>

</div>
<script src="responsive.js"></script>
</body>
</html>