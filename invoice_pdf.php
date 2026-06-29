<?php
session_start();
require "include/db.php";
require "dompdf/autoload.inc.php";

use Dompdf\Dompdf;

// Pastikan param id ada
if (!isset($_GET['id'])) {
    die("Invoice tidak valid.");
}

$ID_Order = intval($_GET['id']);

// Ambil data order
$order = mysqli_query($conn, "
    SELECT o.*, u.Username, u.Email
    FROM orders o
    LEFT JOIN users u ON o.ID_User = u.ID_User
    WHERE o.ID_Order = $ID_Order
");

$d = mysqli_fetch_assoc($order);

// Cek apakah order ada
if (!$d) {
    die("Order tidak ditemukan.");
}

// Cek apakah invoice adalah milik user yg login
if ($d['ID_User'] != $_SESSION['user_id']) {
    die("Anda tidak memiliki akses ke invoice ini.");
}

// CEK STATUS → hanya boleh print jika sudah disetujui admin
if ($d['Status'] == "Pending") {
    die("<h2 style='text-align:center;color:#c0392b;font-family:Arial,sans-serif;padding:50px;'>⚠️ Invoice belum bisa dicetak karena pesanan masih PENDING.</h2>");
}

$items = mysqli_query($conn, "SELECT * FROM order_items WHERE ID_Order=$ID_Order");

// =================== TEMPLATE HTML ===================
$html = "
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<style>
/* ==================== GLOBAL ==================== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'DejaVu Sans', sans-serif;
    background: #f5f6fa;
    padding: 20px !important;
    font-size: 13px !important;
}

/* ==================== INVOICE BOX ==================== */
.invoice-box {
    max-width: 750px;
    margin: 0 auto;
    background: #fff;
    padding: 30px !important;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,.08);
}

/* ==================== HEADER ==================== */
h1 {
    text-align: center;
    margin-bottom: 8px !important;
    font-size: 26px !important;
    font-weight: 700;
    color: #2c3e50;
}

p.header {
    text-align: center;
    color: #1f2727ff;
    margin-bottom: 6px !important;
    font-size: 14px !important;
}

hr {
    border: none;
    border-top: 1px solid #ecf0f1;
    margin: 15px 0 !important;
}

/* ==================== SECTIONS ==================== */
h3 {
    margin-top: 20px !important;
    font-size: 17px !important;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 12px !important;
}

/* ==================== TABLE ==================== */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px !important;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 5px rgba(0,0,0,.05);
    font-size: 12px !important;
}

.table th {
    text-align: left;
    background: #f0393d;
    color: #fff;
    padding: 12px !important;
    font-weight: 600;
    font-size: 12px !important;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.table td {
    padding: 10px 12px !important;
    border-bottom: 1px solid #ecf0f1;
    color: #2c3e50;
    font-size: 12px !important;
}

.table tr:last-child td {
    border-bottom: none;
}

/* ==================== TOTAL PRICE ==================== */
.total-price {
    font-size: 22px !important;
    font-weight: 700 !important;
    color: #f0393d !important;
    text-align: center !important;
    margin: 20px 0 !important;
}

/* ==================== INFO ==================== */
.info-item {
    line-height: 1.6 !important;
    margin-bottom: 6px !important;
    color: #2c3e50;
    font-size: 13px !important;
}

.info-item strong {
    color: #34495e;
    min-width: 140px !important;
    display: inline-block;
    font-size: 13px !important;
}

/* ==================== FOOTER ==================== */
.footer-info {
    margin-top: 25px !important;
    padding-top: 15px !important;
    border-top: 1px solid #ecf0f1;
    text-align: center;
    font-size: 11px !important;
    color: #7f8c8d;
}

/* ==================== PAGE BREAK CONTROL ==================== */
@media print {
    .invoice-box {
        box-shadow: none !important;
        margin: 0 !important;
        padding: 20px !important;
    }
    
    body {
        padding: 10px !important;
        background: white !important;
    }
}

/* ==================== UTILITIES ==================== */
.mb-1 { margin-bottom: 5px !important; }
.mb-2 { margin-bottom: 10px !important; }
.mb-3 { margin-bottom: 15px !important; }
.mt-1 { margin-top: 5px !important; }
.mt-2 { margin-top: 10px !important; }
.mt-3 { margin-top: 15px !important; }
.text-center { text-align: center !important; }
</style>
</head>
<body>

<div class='invoice-box'>

    <h1>Invoice Pemesanan</h1>
    <p class='header'>JourneyMerapi • Bukti Reservasi Tour Wisata</p>

    <hr class='mb-2'>

    <div class='mb-3'>
        <h3>Informasi Pemesan</h3>
        <p class='info-item'><strong>Nama:</strong> " . htmlspecialchars($d['full_name']) . "</p>
        <p class='info-item'><strong>Username:</strong> " . htmlspecialchars($d['Username']) . "</p>
        <p class='info-item'><strong>Email:</strong> " . htmlspecialchars($d['Email']) . "</p>
        <p class='info-item'><strong>Telepon:</strong> " . htmlspecialchars($d['phone']) . "</p>
        <p class='info-item'><strong>Negara:</strong> " . htmlspecialchars($d['country']) . "</p>
        <p class='info-item'><strong>Pickup:</strong> " . date('d/m/Y', strtotime($d['pickup_date'])) . " — " . $d['pickup_time'] . "</p>
        <p class='info-item'><strong>Jumlah Orang:</strong> " . $d['Jumlah_orang'] . " orang</p>
        <p class='info-item mb-2'><strong>Lokasi Pickup:</strong> " . htmlspecialchars($d['pickup_location']) . "</p>";

// Tambahkan Special Request jika ada
if (!empty($d['special_request'])) {
    $html .= "
        <p class='info-item'><strong>Permintaan Khusus:</strong> " . nl2br(htmlspecialchars($d['special_request'])) . "</p>";
}

$html .= "
    </div>

    <h3>Detail Destinasi</h3>
    <table class='table'>
        <thead>
            <tr>
                <th>Destinasi</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>";

while ($i = mysqli_fetch_assoc($items)) {
    $html .= "
            <tr>
                <td>" . htmlspecialchars($i['Tour_Name']) . "</td>
                <td>IDR " . number_format($i['Price'],0,",",".") . "</td>
                <td>" . $i['Quantity'] . " pax</td>
                <td><strong>IDR " . number_format($i['Subtotal'],0,",",".") . "</strong></td>
            </tr>";
}

$html .= "
        </tbody>
    </table>

    <div class='section-box mt-3'>
        <h3>Total Pembayaran</h3>
        <p class='total-price'>IDR " . number_format($d['total_price'],0,",",".") . "</p>

        <div class='footer-info'>
            <p class='mb-1'>Dokumen ini digenerate pada " . date('d/m/Y H:i') . " WIB</p>
            <p>Terima kasih telah memesan di JourneyMerapi!</p>
        </div>
    </div>

</div>

</body>
</html>";

// =================== GENERATE PDF ===================
$dompdf = new Dompdf();

// Set options
$dompdf->set_option('defaultPaperSize', 'A4');
$dompdf->set_option('defaultFont', 'DejaVu Sans');
$dompdf->set_option('isHtml5ParserEnabled', true);
$dompdf->set_option('isPhpEnabled', true);
$dompdf->set_option('isRemoteEnabled', true);
$dompdf->set_option('dpi', 96);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');

// Render
$dompdf->render();

// Download PDF
$filename = "Invoice_" . $d['ID_Order'] . "_" . date('Ymd_His') . ".pdf";
$dompdf->stream($filename, array("Attachment" => true));
?>