<?php
session_start();
require "include/db.php";

if (!isset($_SESSION['user_id'])) {
    die("Akses ditolak. Anda harus login!");
}

$ID_User = $_SESSION['user_id'];

// ===== Ambil data dari form =====
$tours           = json_decode($_POST['tours'], true);
$jumlah_orang    = intval($_POST['jumlah_orang']);
$pickup_date     = $_POST['pickup_date'];
$pickup_time     = $_POST['pickup_time'];
$total_price     = intval($_POST['total_price']);
$pickup_location = "Meeting Point Merapi";

// Data lengkap user
$full_name        = $_POST['full_name'];
$country          = $_POST['country'];
$email            = $_POST['email'];
$phone            = $_POST['phone'];
$special_request  = $_POST['special_request'];

// Validasi
if (!$tours || count($tours) == 0) {
    die("Tidak ada destinasi yang dipilih.");
}

// ===== INSERT ke tabel ORDERS =====
$stmt = $conn->prepare("
    INSERT INTO orders 
    (ID_User, full_name, country, email, phone, special_request,
    Jumlah_orang, pickup_date, pickup_time, pickup_location, total_price, Status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
");

$stmt->bind_param(
    "isssssisssi",
    $ID_User,
    $full_name,
    $country,
    $email,
    $phone,
    $special_request,
    $jumlah_orang,
    $pickup_date,
    $pickup_time,
    $pickup_location,
    $total_price
);

$stmt->execute();
$ID_Order = $stmt->insert_id;


// ===== INSERT setiap destinasi ke ORDER_ITEMS =====
$item = $conn->prepare("
    INSERT INTO order_items (ID_Order, Tour_Name, Price, Quantity, Subtotal)
    VALUES (?, ?, ?, ?, ?)
");

foreach ($tours as $t) {
    $tour_id  = intval($t['id']);
    $name     = $t['name'];
    $price    = intval($t['price']);
    $subtotal = $price * $jumlah_orang;

    $item->bind_param(
        "isiii",
        $ID_Order,
        $name,
        $price,
        $jumlah_orang,
        $subtotal
    );

    $item->execute();
}


// ===== Sukses - Tampilkan halaman sukses =====
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi Berhasil - JourneyMerapi</title>
    <link rel="stylesheet" href="Css/responsive.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ==================== GLOBAL STYLES ==================== */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1d23 0%, #2d1f1f 50%, #3d1f1f 100%);
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Background Pattern */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(240, 57, 61, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(240, 57, 61, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.03) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        /* ==================== SUCCESS CONTAINER ==================== */
        .success-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 60px 50px;
            max-width: 650px;
            width: 100%;
            text-align: center;
            box-shadow: 
                0 25px 70px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            animation: slideIn 0.7s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
            z-index: 1;
            border: 2px solid rgba(240, 57, 61, 0.1);
        }

        @keyframes slideIn {
            from { 
                transform: scale(0.85) translateY(-60px); 
                opacity: 0; 
            }
            to { 
                transform: scale(1) translateY(0); 
                opacity: 1; 
            }
        }

        /* ==================== SUCCESS ICON ==================== */
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #f0393d 0%, #c72e31 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 35px;
            animation: checkPop 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.3s both;
            box-shadow: 
                0 15px 40px rgba(240, 57, 61, 0.4),
                0 0 0 8px rgba(240, 57, 61, 0.1);
            position: relative;
        }

        .success-icon::before {
            content: '';
            position: absolute;
            inset: -15px;
            border-radius: 50%;
            border: 2px solid rgba(240, 57, 61, 0.2);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes checkPop {
            0% { transform: scale(0) rotate(-180deg); }
            60% { transform: scale(1.15) rotate(10deg); }
            100% { transform: scale(1) rotate(0); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.15); opacity: 0.5; }
        }

        .success-icon svg {
            width: 50px;
            height: 50px;
            stroke: white;
            stroke-width: 4;
            stroke-linecap: round;
            stroke-linejoin: round;
            animation: drawCheck 0.8s ease 0.5s both;
        }

        @keyframes drawCheck {
            0% { stroke-dasharray: 0, 100; }
            100% { stroke-dasharray: 100, 0; }
        }

        /* ==================== TEXT CONTENT ==================== */
        h1 {
            font-size: 38px;
            color: #1a1a1a;
            margin-bottom: 12px;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        h1 .emoji {
            display: inline-block;
            animation: bounce 1s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
            font-weight: 500;
        }

        /* ==================== ORDER INFO ==================== */
        .order-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #fff5f5 100%);
            border-radius: 16px;
            padding: 25px 30px;
            margin-bottom: 35px;
            border: 2px solid rgba(240, 57, 61, 0.1);
            box-shadow: 0 4px 20px rgba(240, 57, 61, 0.08);
        }

        .order-id {
            font-size: 15px;
            color: #666;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .order-id-value {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #f0393d 0%, #ff6b6b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 20px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);
            border: 2px solid #ffb74d;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            color: #ff9800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .status-badge svg {
            width: 18px;
            height: 18px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* ==================== MESSAGE ==================== */
        .message {
            font-size: 16px;
            color: #555;
            line-height: 1.8;
            margin-bottom: 40px;
            padding: 0 10px;
        }

        .message strong {
            color: #f0393d;
            font-weight: 700;
        }

        .message-highlight {
            background: linear-gradient(to right, rgba(240, 57, 61, 0.1) 0%, transparent 100%);
            border-left: 4px solid #f0393d;
            padding: 15px 20px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 15px;
            text-align: left;
        }

        /* ==================== BUTTONS ==================== */
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .btn {
            padding: 16px 36px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            border: none;
            letter-spacing: 0.3px;
        }

        .btn svg {
            width: 20px;
            height: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f0393d 0%, #c72e31 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(240, 57, 61, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(240, 57, 61, 0.5);
            background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%);
        }

        .btn-secondary {
            background: white;
            color: #f0393d;
            border: 2px solid #f0393d;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary:hover {
            background: #f0393d;
            color: white;
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(240, 57, 61, 0.3);
        }

        /* ==================== FOOTER INFO ==================== */
        .footer-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 35px;
            padding-top: 30px;
            border-top: 2px solid rgba(0, 0, 0, 0.08);
            font-size: 14px;
            color: #888;
        }

        .footer-info svg {
            width: 20px;
            height: 20px;
            stroke: #f0393d;
        }

        /* ==================== LOGO ==================== */
        .logo {
            margin-top: 25px;
            font-size: 18px;
            font-weight: 700;
            color: #999;
        }

        .logo span {
            color: #666;
        }

        .logo b {
            color: #f0393d;
        }

        /* ==================== DARK MODE STYLES ==================== */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #0a0b0d 0%, #1a1517 50%, #2a1517 100%);
            }

            .success-container {
                background: rgba(26, 29, 35, 0.98);
                border-color: rgba(240, 57, 61, 0.2);
            }

            h1 {
                color: #f0f2f5;
            }

            .subtitle,
            .message {
                color: #b8bdc6;
            }

            .order-info {
                background: linear-gradient(135deg, #242830 0%, #2f252f 100%);
                border-color: rgba(240, 57, 61, 0.2);
            }

            .order-id {
                color: #8a94a6;
            }

            .message-highlight {
                background: rgba(240, 57, 61, 0.15);
            }

            .footer-info {
                color: #6c7280;
                border-top-color: rgba(255, 255, 255, 0.08);
            }

            .logo {
                color: #6c7280;
            }

            .logo span {
                color: #8a94a6;
            }
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 600px) {
            .success-container { 
                padding: 45px 30px; 
                border-radius: 20px;
            }
            
            h1 { 
                font-size: 30px; 
            }
            
            .order-id-value {
                font-size: 26px;
            }
            
            .button-group { 
                flex-direction: column; 
            }
            
            .btn { 
                width: 100%; 
                justify-content: center; 
                padding: 14px 28px;
            }

            .footer-info {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <!-- SUCCESS ICON -->
        <div class="success-icon">
            <svg viewBox="0 0 24 24" fill="none">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        
        <!-- HEADING -->
        <h1>
            <span class="emoji"> Reservasi Berhasil </span>
        </h1>
        
        <p class="subtitle">Terima kasih telah memesan di JourneyMerapi</p>
        
        <!-- ORDER INFO -->
        <div class="order-info">
            <p class="order-id">ID Pemesanan Anda:</p>
            <span class="order-id-value">#<?= str_pad($ID_Order, 6, '0', STR_PAD_LEFT) ?></span>
            
            <div class="status-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Menunggu Konfirmasi
            </div>
        </div>
        
        <!-- MESSAGE -->
        <div class="message">
            Pemesanan Anda telah <strong>tersimpan dengan sukses!</strong>
            
            <div class="message-highlight">
                <strong>Langkah Selanjutnya:</strong><br>
                • Cek email/WhatsApp untuk konfirmasi dari admin<br>
                • Status pemesanan akan diupdate dalam 1x24 jam<br>
                • Silakan cek <strong>Riwayat Pemesanan</strong> untuk detail lengkap
            </div>
        </div>
        
        <!-- BUTTONS -->
        <div class="button-group">
            <a href="my_order.php" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                Lihat Riwayat Pesanan
            </a>
            
            <a href="order.php" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Pesan Lagi
            </a>
        </div>
        
        <!-- FOOTER INFO -->
        <div class="footer-info">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>Simpan ID pemesanan Anda untuk referensi</span>
        </div>
        
        <!-- LOGO -->
        <div class="logo">
            <span>Journey</span><b>Merapi</b>
        </div>
    </div>

    <!-- CONFETTI EFFECT (Optional) -->
    <script>
        // Confetti effect saat halaman load
        function createConfetti() {
            const colors = ['#f0393d', '#ff6b6b', '#ff4757', '#ffa500', '#ffcc00'];
            const confettiCount = 50;
            
            for (let i = 0; i < confettiCount; i++) {
                const confetti = document.createElement('div');
                confetti.style.cssText = `
                    position: fixed;
                    width: 10px;
                    height: 10px;
                    background: ${colors[Math.floor(Math.random() * colors.length)]};
                    top: -10px;
                    left: ${Math.random() * 100}%;
                    opacity: ${Math.random()};
                    transform: rotate(${Math.random() * 360}deg);
                    animation: fall ${3 + Math.random() * 2}s linear forwards;
                    z-index: 9999;
                    pointer-events: none;
                `;
                
                document.body.appendChild(confetti);
                
                setTimeout(() => confetti.remove(), 5000);
            }
        }
        
        // CSS animation untuk confetti
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fall {
                to {
                    transform: translateY(105vh) rotate(${Math.random() * 360}deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
        
        // Trigger confetti saat halaman load
        window.addEventListener('load', () => {
            setTimeout(createConfetti, 500);
        });
    </script>
    <script src="responsive.js"></script>
</body>
</html>