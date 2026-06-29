<?php
session_start();
require "include/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$ID_User = $_SESSION['user_id'];

$q = mysqli_query($conn,"
    SELECT * FROM orders 
    WHERE ID_User=$ID_User
    ORDER BY Created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Riwayat Pemesanan - JourneyMerapi</title>

<!-- CSS Files -->
<link rel="stylesheet" href="Css/style.css" />
<link rel="stylesheet" href="Css/darkmode.css" />
<link rel="stylesheet" href="Css/responsive.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ==================== LIGHT MODE STYLES ==================== */
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f8f9fc 0%, #f0f2f8 100%);
    margin: 0;
    padding: 50px 30px;
    min-height: 100vh;
    transition: all 0.4s ease;
}

/* HEADER SECTION */
.header-section {
    max-width: 1200px;
    margin: 0 auto 40px;
    padding: 20px 0;
}

/* JUDUL UTAMA */
.page-title {
    font-size: 42px;
    font-weight: 800;
    margin-bottom: 10px;
    background: linear-gradient(135deg, #ff2e2e 0%, #ff6b6b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.5px;
    line-height: 1.2;
}

/* BACK BUTTON */
.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 17px;
    color: #ff4757;
    font-weight: 600;
    text-decoration: none;
    padding: 10px 20px;
    background: rgba(255, 71, 87, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}
.back-btn:hover {
    background: rgba(255, 71, 87, 0.15);
    color: #ff2e43;
    transform: translateY(-2px);
    border-color: rgba(255, 71, 87, 0.2);
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(255, 71, 87, 0.15);
}

/* NO ORDERS MESSAGE */
.no-orders {
    text-align: center;
    padding: 80px 20px;
    color: #666;
    font-size: 18px;
    font-weight: 500;
    background: white;
    border-radius: 16px;
    max-width: 600px;
    margin: 40px auto;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    border: 1px solid #eaeaea;
}

/* ==================== ORDER CARDS ==================== */
.order-container {
    max-width: 1200px;
    margin: 0 auto;
}

.order-card {
    background: white;
    border-radius: 18px;
    padding: 28px 32px;
    margin-bottom: 25px;
    border: 1px solid #e8e8e8;
    box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.order-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
    background: linear-gradient(to bottom, #ff4757, #ff6b6b);
    border-radius: 5px 0 0 5px;
}

.order-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(255, 71, 87, 0.15);
    border-color: #ffd1d5;
}

/* DESTINATION TITLE */
.order-header {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.order-header::before {
    content: '📍';
    font-size: 20px;
}

/* INFO TEXT */
.order-info {
    font-size: 16px;
    line-height: 1.7;
    color: #444;
    margin-bottom: 15px;
}

.order-info b {
    color: #222;
    font-weight: 600;
}

/* STATUS BADGES */
.status {
    padding: 8px 18px;
    border-radius: 12px;
    font-weight: 700;
    display: inline-block;
    font-size: 14px;
    margin-top: 5px;
    letter-spacing: 0.3px;
    border: 2px solid;
    transition: all 0.3s ease;
}

.pending {
    background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);
    color: #ff9800;
    border-color: #ffb74d;
}
.pending:hover {
    background: linear-gradient(135deg, #ffecb3 0%, #ffe082 100%);
    transform: scale(1.05);
}

.process {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    color: #2196f3;
    border-color: #64b5f6;
}
.process:hover {
    background: linear-gradient(135deg, #bbdefb 0%, #90caf9 100%);
    transform: scale(1.05);
}

.done {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    color: #4caf50;
    border-color: #81c784;
}
.done:hover {
    background: linear-gradient(135deg, #c8e6c9 0%, #a5d6a7 100%);
    transform: scale(1.05);
}

/* BUTTONS SECTION */
.card-buttons {
    margin-top: 22px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 24px;
    border-radius: 12px;
    text-decoration: none;
    color: white !important;
    font-weight: 600;
    font-size: 15px;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.btn-detail {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}
.btn-detail:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004494 100%);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.25);
}

.btn-invoice {
    background: linear-gradient(135deg, #28c76f 0%, #1e9e57 100%);
}
.btn-invoice:hover {
    background: linear-gradient(135deg, #1e9e57 0%, #178046 100%);
    box-shadow: 0 6px 20px rgba(40, 199, 111, 0.25);
}

.btn-disabled {
    background: linear-gradient(135deg, #a0a0a0 0%, #808080 100%);
    cursor: not-allowed;
    opacity: 0.7;
}
.btn-disabled:hover {
    transform: none !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

/* ==================== MODAL STYLES ==================== */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    padding: 20px;
    backdrop-filter: blur(4px);
}

.modal.active {
    display: flex;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-box {
    background: white;
    width: 500px;
    max-width: 90%;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    position: relative;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-title {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

#detailBody {
    font-size: 15px;
    line-height: 1.7;
    color: #444;
}

.close-btn {
    background: linear-gradient(135deg, #ff4757 0%, #ff2e43 100%);
    color: white;
    padding: 14px 0;
    width: 100%;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    margin-top: 25px;
    transition: all 0.3s ease;
    letter-spacing: 0.5px;
}

.close-btn:hover {
    background: linear-gradient(135deg, #ff2e43 0%, #e02437 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 71, 87, 0.25);
}

/* ==================== DARK MODE OVERRIDES ==================== */
body.dark-mode {
    background: linear-gradient(135deg, #1a1d23 0%, #23272f 100%) !important;
}

body.dark-mode .page-title {
    background: linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
}

body.dark-mode .back-btn {
    color: #ff6b6b !important;
    background: rgba(255, 107, 107, 0.1) !important;
    border-color: rgba(255, 107, 107, 0.2) !important;
}
body.dark-mode .back-btn:hover {
    background: rgba(255, 107, 107, 0.2) !important;
    color: #ff4757 !important;
    border-color: rgba(255, 107, 107, 0.3) !important;
}

body.dark-mode .no-orders {
    background: #242830 !important;
    color: #b8bdc6 !important;
    border-color: #383e4a !important;
}

/* ORDER CARDS - DARK MODE */
body.dark-mode .order-card {
    background: #2a2f3a !important;
    border: 1px solid #383e4a !important;
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25) !important;
}

body.dark-mode .order-card:hover {
    box-shadow: 0 15px 45px rgba(255, 71, 87, 0.2) !important;
    border-color: #ff4757 !important;
}

body.dark-mode .order-header {
    color: #f0f2f5 !important;
}

body.dark-mode .order-info {
    color: #b8bdc6 !important;
}
body.dark-mode .order-info b {
    color: #e8eaed !important;
}

/* STATUS BADGES - DARK MODE */
body.dark-mode .status {
    background: transparent !important;
    border-width: 2px !important;
}

body.dark-mode .pending {
    background: rgba(255, 193, 7, 0.12) !important;
    color: #ffc107 !important;
    border-color: #ffc107 !important;
}
body.dark-mode .pending:hover {
    background: rgba(255, 193, 7, 0.2) !important;
}

body.dark-mode .process {
    background: rgba(3, 169, 244, 0.12) !important;
    color: #03a9f4 !important;
    border-color: #03a9f4 !important;
}
body.dark-mode .process:hover {
    background: rgba(3, 169, 244, 0.2) !important;
}

body.dark-mode .done {
    background: rgba(76, 175, 80, 0.12) !important;
    color: #4caf50 !important;
    border-color: #4caf50 !important;
}
body.dark-mode .done:hover {
    background: rgba(76, 175, 80, 0.2) !important;
}

/* MODAL - DARK MODE */
body.dark-mode .modal {
    background: rgba(0, 0, 0, 0.7) !important;
    backdrop-filter: blur(8px) saturate(180%);
}

body.dark-mode .modal-box {
    background: #2f3541 !important;
    border: 1px solid #3d4350;
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
}

/* JUDUL MODAL DARK MODE */
body.dark-mode .modal-title {
    color: #f0f2f5 !important;
    border-bottom-color: #3d4350 !important;
}

/* DETAIL BODY DARK MODE */
body.dark-mode #detailBody {
    color: #b8bdc6 !important;
}

/* TABEL DARK MODE - STYLING LENGKAP */
body.dark-mode #detailBody table {
    border: 1px solid #3d4350 !important;
    border-collapse: collapse !important;
    width: 100% !important;
    border-radius: 8px !important;
    overflow: hidden !important;
}

body.dark-mode #detailBody thead {
    background: #383e4a !important;
}

body.dark-mode #detailBody th {
    background: #383e4a !important;
    color: #f0f2f5 !important;
    border: 1px solid #4a5060 !important;
    padding: 12px !important;
    font-weight: 600 !important;
    text-align: left !important;
    font-size: 13px !important;
    text-transform: uppercase !important;
}

body.dark-mode #detailBody td {
    background: #2f3541 !important;
    color: #b8bdc6 !important;
    border: 1px solid #3d4350 !important;
    padding: 12px !important;
    font-size: 14px !important;
}

/* BARIS TOTAL - PERBAIKAN KHUSUS */
body.dark-mode #detailBody tr[style*="background"] td,
body.dark-mode #detailBody tr:last-child td {
    background: #383e4a !important;
    color: #e8eaed !important;
    font-weight: 700 !important;
    border-top: 2px solid #4a5060 !important;
}

/* HIGHLIGHT HARGA DARK MODE */
body.dark-mode #detailBody .price-highlight {
    color: #ff6b6b !important;
    font-weight: 600 !important;
}

/* BARIS TOTAL YANG DIHIGHLIGHT */
body.dark-mode #detailBody tr[style*="background"] .price-highlight,
body.dark-mode #detailBody tr:last-child .price-highlight {
    color: #ff6b6b !important;
    font-size: 16px !important;
}

/* HOVER EFFECT DARK MODE */
body.dark-mode #detailBody tr:hover td {
    background: #404754 !important;
}

/* TEXT YANG DI-BOLD DARK MODE */
body.dark-mode #detailBody b,
body.dark-mode #detailBody strong {
    color: #e8eaed !important;
}

/* PESAN KOSONG DARK MODE */
body.dark-mode #detailBody .empty-message {
    color: #8a94a6 !important;
    background: transparent !important;
}

/* TOMBOL TUTUP DARK MODE */
body.dark-mode .close-btn {
    background: linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%) !important;
}
body.dark-mode .close-btn:hover {
    background: linear-gradient(135deg, #ff4757 0%, #ff2e43 100%) !important;
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 768px) {
    body {
        padding: 30px 15px;
    }
    
    .page-title {
        font-size: 32px;
    }
    
    .order-card {
        padding: 22px;
        margin-bottom: 20px;
    }
    
    .order-header {
        font-size: 20px;
    }
    
    .btn {
        padding: 10px 18px;
        font-size: 14px;
    }
    
    .modal-box {
        padding: 25px;
    }
}

@media (max-width: 480px) {
    .card-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
</head>

<body>
<!-- HEADER SECTION -->
<div class="header-section">
    <h1 class="page-title">Riwayat Pemesanan Saya</h1>
    <a class="back-btn" href="order.php">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Kembali ke Order
    </a>
</div>

<!-- ORDER CONTAINER -->
<div class="order-container">
    <?php if (mysqli_num_rows($q) == 0): ?>
        <div class="no-orders">
            <p>Belum ada pemesanan. Mulai pesan tour pertama Anda!</p>
        </div>
    <?php else: ?>
        <?php while ($o = mysqli_fetch_assoc($q)): ?>
            <?php
            $first = mysqli_fetch_assoc(mysqli_query($conn,"
                SELECT * FROM order_items WHERE ID_Order={$o['ID_Order']} LIMIT 1
            "));
            $count = mysqli_fetch_assoc(mysqli_query($conn,"
                SELECT COUNT(*) AS total FROM order_items WHERE ID_Order={$o['ID_Order']}
            "))['total'];
            
            $statusClass = $o['Status'] == "Pending" ? "pending" : 
                          ($o['Status'] == "Diproses" ? "process" : "done");
            ?>
            
            <div class="order-card">
                <div class="order-header">
                    <?= htmlspecialchars($first['Tour_Name'] ?? 'Tour') ?>
                    <?php if ($count > 1): ?>
                        <span style="font-size: 14px; color: #666; font-weight: normal;">
                            + <?= $count - 1 ?> destinasi lain
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="order-info">
                    <b>📅 Pickup:</b> <?= htmlspecialchars($o['pickup_date']) ?> (<?= htmlspecialchars($o['pickup_time']) ?>)<br>
                    <b>👥 Jumlah Orang:</b> <?= $o['Jumlah_orang'] ?><br>
                    <b>💰 Total:</b> IDR <?= number_format($o['total_price'], 0, ",", ".") ?><br>
                    
                    <span class="status <?= $statusClass ?>">
                        <?= htmlspecialchars($o['Status']) ?>
                    </span>
                </div>
                
                <div class="card-buttons">
                    <a href="#" class="btn btn-detail" onclick="showDetail(<?= $o['ID_Order'] ?>)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        Detail
                    </a>
                    
                    <?php if ($o['Status'] != 'Pending'): ?>
                        <a href="invoice.php?id=<?= $o['ID_Order'] ?>" class="btn btn-invoice">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            Invoice
                        </a>
                    <?php else: ?>
                        <span class="btn btn-disabled">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            Pending
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<!-- MODAL DETAIL -->
<div id="detailModal" class="modal">
    <div class="modal-box">
        <h3 class="modal-title">Detail Pemesanan</h3>
        <div id="detailBody" style="font-size:15px; line-height:1.6;">Memuat detail pemesanan...</div>
        <button class="close-btn" onclick="closeDetail()">Tutup</button>
    </div>
</div>

<script src="auth.js"></script>
<script>
// Auto apply dark mode dari localStorage
document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem("darkMode") === "enabled") {
        document.body.classList.add("dark-mode");
        
        // Update button text jika ada
        const darkBtn = document.getElementById("darkModeToggle");
        if (darkBtn) {
            darkBtn.textContent = "Light Mode";
            darkBtn.classList.add('light-mode-btn');
        }
    }
});

// Fungsi untuk show detail
function showDetail(id){
    const modal = document.getElementById("detailModal");
    const body = document.getElementById("detailBody");
    
    modal.classList.add("active");
    body.innerHTML = '<div style="text-align:center;padding:20px;color:#666;"><p>Memuat detail pemesanan...</p></div>';
    
    fetch("admin/order_items_api.php?id=" + id)
        .then(response => response.text())
        .then(html => {
            body.innerHTML = html;
            
            // Apply dark mode styling ke konten modal jika perlu
            if (document.body.classList.contains('dark-mode')) {
                const modalContent = body.querySelectorAll('*');
                modalContent.forEach(el => {
                    if (el.tagName === 'B' || el.tagName === 'STRONG') {
                        el.style.color = '#f0f2f5';
                    } else if (el.tagName === 'P' || el.tagName === 'SPAN') {
                        el.style.color = '#b8bdc6';
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            body.innerHTML = '<div style="text-align:center;padding:20px;color:#ff4757;"><p>⚠️ Terjadi kesalahan saat memuat detail pemesanan.</p></div>';
        });
}

// Fungsi untuk close detail
function closeDetail(){
    document.getElementById("detailModal").classList.remove("active");
}

// Tutup modal saat klik di luar
document.getElementById("detailModal").addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetail();
    }
});

// ESC key untuk tutup modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetail();
    }
});
</script>
<script src="responsive.js"></script>
</body>
</html>