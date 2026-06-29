<?php
session_start();
require "include/db.php";

// CEK STATUS LOGIN DI PHP
$isLoggedIn = isset($_SESSION['user_id']);

// Ambil semua tour dari database
$tourQuery = mysqli_query($conn, "SELECT * FROM tour ORDER BY ID_Tour ASC");
$tours = [];
while ($row = mysqli_fetch_assoc($tourQuery)) {
    $tours[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>JourneyMerapi - Pemesanan Tiket Tour</title>

    <link rel="stylesheet" href="Css/style.css" />
    <link rel="stylesheet" href="Css/darkmode.css" />
    <link rel="stylesheet" href="Css/navbar.css" />
    <link rel="stylesheet" href="Css/animations.css" />
    <link rel="stylesheet" href="Css/modal.css" />
    <link rel="stylesheet" href="Css/order.css" />
    <link rel="stylesheet" href="Css/responsive.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Tombol My Orders (hanya di halaman order) */
        .order-top-btn {
            display: flex;
            justify-content: flex-end;
            margin: 10px 10px 25px 10px;
        }
        .btn-myorders {
            background: #db0000ff;
            color: #fff !important;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: .25s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-myorders:hover {
            background: #ffffffff;
            transform: translateY(-3px);
            color: #db0000ff !important;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
    </style>
</head>

<body class="fade-in">

<!-- ================= NAVBAR ================= -->
<header>
    <nav class="navbar">
        <div class="logo-about"><span>Journey</span><b>Merapi</b></div>

        <ul class="menu-about">
            <li><a href="index.php">Beranda</a></li>
            <li><a href="about.php">Tentang</a></li>
            <li><a href="tour.php">Tour</a></li>
            <li><a href="order.php" class="active">Order Now</a></li>
            <li><a href="contact.php">Kontak</a></li>
            <li><a href="status.php">Status Merapi</a></li>
        </ul>

        <div class="nav-btns">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <button id="openLogin" class="btn login">Login</button>
            <?php else: ?>
                <span style="margin-right:10px;">Halo, <?= $_SESSION['username'] ?></span>
                <a href="auth/logout.php" class="btn logout">Logout</a>
            <?php endif; ?>

            <button id="darkModeToggle" class="btn dark">Dark Mode</button>
        </div>
    </nav>
</header>

<!-- ================= FORM ORDER ================= -->
<section class="order-section">
    <h1>Pesan Tiket Tour <span>Online Sekarang Juga!!</span></h1>
    <p>Isi form dibawah untuk memesan tiket online Tour Wisata Merapi</p>

    <!-- ========== TOMBOL MY ORDERS DI SINI ========== -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="order-top-btn">
        <a href="my_order.php" class="btn-myorders">📦 Riwayat Pesanan Saya</a>
    </div>
    <?php endif; ?>
    <!-- =============================================== -->

    <div class="container">

        <!-- PILIH DESTINASI -->
        <div class="form-section">
            <div class="section-title">Pilih Destinasi (Bisa memilih lebih dari satu):</div>

            <?php foreach ($tours as $t): ?>
                <div class="tour-option">
                    <input type="checkbox"
                        name="tour"
                        id="tour-<?= $t['ID_Tour'] ?>"
                        value="<?= $t['Nama'] ?>"
                        data-id="<?= $t['ID_Tour'] ?>"
                        data-price="<?= $t['Harga_mulai'] ?>">

                    <label for="tour-<?= $t['ID_Tour'] ?>"><?= $t['Nama'] ?></label>
                    <span class="tour-price">IDR <?= number_format($t['Harga_mulai'], 0, ',', '.') ?></span>
                </div>
            <?php endforeach; ?>

            <div class="selected-tours" id="selected-tours-info">
                <h3>Destinasi yang Dipilih:</h3>

                <div id="selected-tours-list">
                    <p class="no-selection">Belum ada destinasi yang dipilih</p>
                </div>

                <div class="total-price">
                    <span>Total:</span>
                    <span id="total-price">IDR 0</span>
                </div>
            </div>
        </div>

        <hr>

        <!-- PICKUP -->
        <div class="form-section">
            <div class="section-title">Pickup Schedule:</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Date Pickup:</label>
                    <input type="date" id="pickup-date" required>
                </div>

                <div class="form-group">
                    <label>Time Pickup:</label>
                    <select id="pickup-time" required>
                        <option value="">Pilih waktu pickup</option>
                        <option>08:00</option>
                        <option>09:00</option>
                        <option>10:00</option>
                        <option>11:00</option>
                        <option>13:00</option>
                        <option>14:00</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- PERSONAL INFO -->
        <div class="form-section">
            <div class="section-title">Personal Info</div>

            <div class="form-row">
                <input type="number" id="jumlah-orang" min="1" placeholder="Jumlah Orang" required>
            </div>

            <div class="form-row">
                <input type="text" id="full-name" placeholder="Full Name" required>
                <input type="text" id="country" placeholder="Country" required>
            </div>

            <div class="form-row">
                <input type="email" id="email" placeholder="Email" required>
                <input type="tel" id="phone" placeholder="Phone" required>
            </div>
        </div>

        <!-- SPECIAL REQUEST -->
        <div class="form-section">
            <div class="section-title">Special Request</div>
            <textarea id="special-request" placeholder="Masukkan detail tambahan..."></textarea>
        </div>

        <!-- FORM HIDDEN -->
        <form id="orderForm" method="POST" action="order_process.php">
            <input type="hidden" name="tours" id="toursInput">
            <input type="hidden" name="total_price" id="totalPriceInput">
            <input type="hidden" name="pickup_date" id="pickupDateInput">
            <input type="hidden" name="pickup_time" id="pickupTimeInput">
            <input type="hidden" name="jumlah_orang" id="jumlahOrangInput">
            <input type="hidden" name="full_name" id="fullNameInput">
            <input type="hidden" name="country" id="countryInput">
            <input type="hidden" name="email" id="emailInput">
            <input type="hidden" name="phone" id="phoneInput">
            <input type="hidden" name="special_request" id="specialRequestInput">
        </form>

        <button class="btn-reserve" id="reserve-btn">Reservasi Sekarang</button>

    </div>
</section>

<!-- FOOTER -->
<footer>
    <p>© 2025 JourneyMerapi. All rights reserved.</p>
</footer>

<!-- LOAD MODAL DAN AUTH.JS SEKALI SAJA -->
<?php include "include/modal.php"; ?>
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
    }, type === 'success' ? 6000 : 4000);
}

// Format Rupiah
function rupiah(v){ return "IDR " + v.toLocaleString("id-ID"); }

// Update pilihan destinasi
function updateSelected() {
    const list = document.getElementById("selected-tours-list");
    const totalEl = document.getElementById("total-price");
    const selected = [...document.querySelectorAll("input[name='tour']:checked")];

    list.innerHTML = "";
    let total = 0;

    if (selected.length === 0) {
        list.innerHTML = `<p class='no-selection'>Belum ada destinasi yang dipilih</p>`;
    } else {
        selected.forEach(c => {
            const price = parseInt(c.dataset.price);
            total += price;

            list.innerHTML += `
                <div class="selected-tour-item">
                    <span>${c.value}</span>
                    <span>${rupiah(price)}</span>
                </div>`;
        });
    }

    totalEl.innerText = rupiah(total);
}

document.querySelectorAll("input[name='tour']").forEach(c =>
    c.addEventListener("change", updateSelected)
);

// FUNGSI UNTUK MENYIMPAN DATA FORM
function saveOrderFormData() {
    const formData = {
        selectedTours: [...document.querySelectorAll("input[name='tour']:checked")].map(c => c.dataset.id),
        pickupDate: document.getElementById("pickup-date").value,
        pickupTime: document.getElementById("pickup-time").value,
        jumlahOrang: document.getElementById("jumlah-orang").value,
        fullName: document.getElementById("full-name").value,
        country: document.getElementById("country").value,
        email: document.getElementById("email").value,
        phone: document.getElementById("phone").value,
        specialRequest: document.getElementById("special-request").value
    };
    
    if (typeof window.saveFormState === 'function') {
        window.saveFormState('order_form', formData);
    }
}

// FUNGSI UNTUK RESTORE DATA FORM
function restoreOrderFormData() {
    if (typeof window.restoreFormState !== 'function') {
        console.log('restoreFormState function not ready yet');
        return;
    }
    
    const formData = window.restoreFormState('order_form');
    
    if (!formData) return;
    
    // Restore selected tours
    if (formData.selectedTours && formData.selectedTours.length > 0) {
        formData.selectedTours.forEach(tourId => {
            const checkbox = document.querySelector(`input[data-id="${tourId}"]`);
            if (checkbox) checkbox.checked = true;
        });
        updateSelected();
    }
    
    // Restore form fields
    if (formData.pickupDate) document.getElementById("pickup-date").value = formData.pickupDate;
    if (formData.pickupTime) document.getElementById("pickup-time").value = formData.pickupTime;
    if (formData.jumlahOrang) document.getElementById("jumlah-orang").value = formData.jumlahOrang;
    if (formData.fullName) document.getElementById("full-name").value = formData.fullName;
    if (formData.country) document.getElementById("country").value = formData.country;
    if (formData.email) document.getElementById("email").value = formData.email;
    if (formData.phone) document.getElementById("phone").value = formData.phone;
    if (formData.specialRequest) document.getElementById("special-request").value = formData.specialRequest;
    
    showStyledNotification('Data form Anda telah dipulihkan. Silakan lanjutkan pemesanan.', 'info');
}

// HANDLE AUTO-SELECT DAN RESTORE
window.addEventListener('load', function() {
    setTimeout(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const tourId = urlParams.get('tour');
        
        if (tourId) {
            const checkbox = document.querySelector(`input[data-id="${tourId}"]`);
            
            if (checkbox) {
                checkbox.checked = true;
                updateSelected();
                
                setTimeout(() => {
                    document.querySelector('.selected-tours').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 300);
                
                showStyledNotification(`${checkbox.value} telah dipilih. Lengkapi form untuk melanjutkan.`, 'success');
            }
            
            window.history.replaceState({}, '', window.location.pathname);
        }
        
        restoreOrderFormData();
    }, 100);
});

// SUBMIT ORDER - DENGAN VALIDASI LOGIN
document.getElementById("reserve-btn").addEventListener("click", function () {
    const selected = [...document.querySelectorAll("input[name='tour']:checked")];
    
    if (selected.length === 0) { 
        showStyledNotification("Pilih minimal 1 destinasi!", 'warning');
        return; 
    }

    const jumlah = document.getElementById("jumlah-orang").value;
    const date = document.getElementById("pickup-date").value;
    const time = document.getElementById("pickup-time").value;
    const fullName = document.getElementById("full-name").value;
    const country = document.getElementById("country").value;
    const email = document.getElementById("email").value;
    const phone = document.getElementById("phone").value;

    if (!jumlah || !date || !time || !fullName || !country || !email || !phone) {
        showStyledNotification("Lengkapi semua data wajib!", 'warning');
        return;
    }

    // CEK LOGIN MENGGUNAKAN VARIABEL JAVASCRIPT DARI PHP
    if (!isUserLoggedIn) {
        saveOrderFormData();
        
        // Tampilkan notifikasi styled
        showStyledNotification('Silakan login terlebih dahulu untuk melanjutkan reservasi.', 'warning');
        
        // Buka modal login
        setTimeout(() => {
            const loginModal = document.getElementById('loginModal');
            if (loginModal) {
                loginModal.classList.add('active');
            }
        }, 600);
        
        return;
    }

    // IKA SUDAH LOGIN, LANJUTKAN SUBMIT
    let toursData = selected.map(c => ({
        id: c.dataset.id,
        name: c.value,
        price: parseInt(c.dataset.price),
        quantity: jumlah,
        subtotal: parseInt(c.dataset.price) * jumlah
    }));

    document.getElementById("toursInput").value = JSON.stringify(toursData);
    document.getElementById("totalPriceInput").value = toursData.reduce((a,b)=>a+b.subtotal,0);
    document.getElementById("pickupDateInput").value = date;
    document.getElementById("pickupTimeInput").value = time;
    document.getElementById("jumlahOrangInput").value = jumlah;
    document.getElementById("fullNameInput").value = fullName;
    document.getElementById("countryInput").value = country;
    document.getElementById("emailInput").value = email;
    document.getElementById("phoneInput").value = phone;
    document.getElementById("specialRequestInput").value = document.getElementById("special-request").value;

    // Clear form state setelah berhasil submit
    if (typeof window.clearFormState === 'function') {
        window.clearFormState('order_form');
    }
    
    document.getElementById("orderForm").submit();
});
</script>
<script src="responsive.js"></script>
</body>
</html>