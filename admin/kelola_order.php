<?php
session_start();
require "../include/db.php";

// Cek akses admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// DELETE ORDER
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM order_items WHERE ID_Order = $id");
    mysqli_query($conn, "DELETE FROM orders WHERE ID_Order = $id");
    header("Location: kelola_order.php");
    exit;
}

// UPDATE STATUS
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id     = intval($_POST["order_id"]);
    $status = mysqli_real_escape_string($conn, $_POST["status"]);
    mysqli_query($conn, "UPDATE orders SET Status='$status' WHERE ID_Order=$id");
    header("Location: kelola_order.php");
    exit;
}

// GET ALL ORDERS (latest first)
$query = "
    SELECT o.*, u.Username
    FROM orders o
    LEFT JOIN users u ON o.ID_User = u.ID_User
    ORDER BY o.Created_at DESC
";
$data = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Pemesanan - Admin JourneyMerapi</title>
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
    min-width: 1400px;
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
    min-width: 1100px;
}

h1 {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
}

.subtitle {
    color: #7f8c8d;
    margin-bottom: 30px;
}

/* ==================== BUTTONS ==================== */
.button-group {
    display: flex;
    gap: 15px;
    margin: 25px 0;
    flex-wrap: wrap;
}

.btn-back {
    display: inline-block;
    padding: 12px 24px;
    background: #95a5a6;
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-back:hover {
    background: #7f8c8d;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(149, 165, 166, 0.3);
}

/* ==================== TABLE WRAPPER ==================== */
.table-wrapper {
    overflow-x: auto;
    margin-top: 20px;
    margin-bottom: 30px;
    border-radius: 12px;
    background: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

/* ==================== TABLE ==================== */
table {
    width: 100%;
    min-width: 1400px;
    border-collapse: collapse;
    background: #fff;
}

thead {
    background: linear-gradient(135deg, #f0393d 0%, #d6282c 100%);
}

th {
    padding: 16px 12px;
    color: white;
    font-weight: 600;
    text-align: left;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

td {
    padding: 14px 12px;
    border-bottom: 1px solid #ecf0f1;
    font-size: 13px;
    color: #2c3e50;
}

tbody tr {
    transition: background 0.2s ease;
}

tbody tr:hover {
    background: #f8f9fa;
}

tr:last-child td {
    border-bottom: none;
}

/* ==================== STATUS BADGE ==================== */
.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
    text-transform: uppercase;
}

.badge-pending { 
    background: #fff3cd; 
    color: #856404; 
}

.badge-diproses { 
    background: #d1ecf1; 
    color: #0c5460; 
}

.badge-selesai { 
    background: #d4edda; 
    color: #155724; 
}

/* ==================== ACTION BUTTONS ==================== */
.action-buttons {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.btn-detail, .btn-status, .btn-delete {
    padding: 7px 12px;
    border-radius: 6px;
    text-decoration: none;
    color: white;
    font-size: 12px;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
    transition: 0.3s;
    cursor: pointer;
    border: none;
    white-space: nowrap;
}

.btn-detail {
    background: #2ecc71;
}

.btn-detail:hover {
    background: #27ae60;
    transform: translateY(-2px);
}

.btn-status {
    background: #3498db;
}

.btn-status:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.btn-delete {
    background: #e74c3c;
}

.btn-delete:hover {
    background: #c0392b;
    transform: translateY(-2px);
}

/* ==================== MODAL ==================== */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    backdrop-filter: blur(5px);
}

.modal.active { 
    display: flex;
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    background: #fff;
    padding: 30px;
    width: 600px;
    max-width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    animation: slideDown 0.3s;
}

@keyframes slideDown {
    from { 
        transform: translateY(-50px);
        opacity: 0;
    }
    to { 
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-content h3 {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
}

.modal-content label {
    display: block;
    font-weight: 600;
    color: #34495e;
    margin-bottom: 8px;
    margin-top: 15px;
}

.modal-content select {
    width: 100%;
    padding: 12px 15px;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    transition: 0.3s;
}

.modal-content select:focus {
    outline: none;
    border-color: #f0393d;
    box-shadow: 0 0 0 3px rgba(240, 57, 61, 0.1);
}

.close-btn, .btn-save {
    background: #f0393d;
    color: white;
    padding: 14px;
    border: none;
    width: 100%;
    border-radius: 10px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    margin-top: 20px;
    transition: 0.3s;
}

.close-btn:hover, .btn-save:hover { 
    background: #d6282c;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(240, 57, 61, 0.3);
}

/* Detail table in modal */
.modal-content table {
    margin-top: 10px;
    min-width: 100%;
}

.modal-content th {
    background: #2c3e50;
    padding: 10px;
}

.modal-content td {
    padding: 10px;
    border: 1px solid #ddd;
}
</style>

</head>
<body>

<!-- ==================== SIDEBAR ==================== -->
<aside class="sidebar">
    <h2 class="logo">Admin <span>Panel</span></h2>

    <ul class="menu">
        <li><a href="dashboard_admin.php">📊 Dashboard</a></li>
        <li><a href="kelola_status_merapi.php">🌋 Status Merapi</a></li>
        <li><a href="kelola_tour.php">🗺️ Tour</a></li>
        <li><a href="kelola_order.php" class="active">📋 Orders</a></li>
        <li><a href="kelola_contact.php">📩 Contact</a></li>
        <li><a href="kelola_user.php">👤 Users</a></li>
    </ul>

    <a href="../auth/logout.php" class="logout-btn">Logout</a>
</aside>

<!-- ==================== MAIN CONTENT ==================== -->
<main class="content">

<h1>Kelola Pemesanan</h1>
<p class="subtitle">Daftar seluruh pemesanan wisata oleh pengguna</p>

<div class="button-group">
    <a href="dashboard_admin.php" class="btn-back">← Kembali</a>
</div>

<div class="table-wrapper">
<table>
<thead>
<tr>
    <th>ID</th>
    <th>User Login</th>
    <th>Nama Lengkap</th>
    <th>Email</th>
    <th>HP</th>
    <th>Negara</th>
    <th>Jumlah</th>
    <th>Total Harga</th>
    <th>Pickup</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>
</thead>
<tbody>

<?php while ($row = mysqli_fetch_assoc($data)): ?>
<tr>
    <td><?= $row['ID_Order'] ?></td>
    <td><?= $row['Username'] ?: "-" ?></td>
    <td><?= htmlspecialchars($row['full_name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= htmlspecialchars($row['phone']) ?></td>
    <td><?= htmlspecialchars($row['country']) ?></td>
    <td><?= $row['Jumlah_orang'] ?> orang</td>
    <td>IDR <?= number_format($row['total_price'],0,",",".") ?></td>
    <td><?= date('d/m/Y', strtotime($row['pickup_date'])) ?><br><small><?= $row['pickup_time'] ?></small></td>
    <td>
        <span class="badge badge-<?= strtolower($row['Status']) ?>">
            <?= $row['Status'] ?>
        </span>
    </td>
    <td>
        <div class="action-buttons">
            <button class="btn-detail" onclick="showDetail(<?= $row['ID_Order'] ?>)">Detail</button>
            <button class="btn-status" onclick='openStatus(<?= json_encode($row) ?>)'>Status</button>
            <a class="btn-delete" href="?delete=<?= $row['ID_Order'] ?>"
               onclick="return confirm('⚠️ Hapus pemesanan ini?')">Hapus</a>
        </div>
    </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>

</main>


<!-- ==================== MODAL DETAIL ==================== -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <h3>Detail Destinasi</h3>
        <div id="detailBody">Memuat...</div>
        <button class="close-btn" onclick="closeDetail()">Tutup</button>
    </div>
</div>

<!-- ==================== MODAL STATUS ==================== -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <h3>Ubah Status Pemesanan</h3>

        <form method="POST">
            <input type="hidden" name="order_id" id="orderID">

            <label>Status Baru:</label>
            <select name="status" id="statusInput" required>
                <option value="Pending">Pending</option>
                <option value="Diproses">Diproses</option>
                <option value="Selesai">Selesai</option>
            </select>

            <button class="btn-save">Simpan</button>
        </form>
    </div>
</div>

<script>
// ===== DETAIL ORDER (AJAX) =====
function showDetail(id) {
    const modal = document.getElementById("detailModal");
    const body  = document.getElementById("detailBody");

    modal.classList.add("active");
    body.innerHTML = "Memuat data...";

    fetch("order_items_api.php?id=" + id)
        .then(r => r.text())
        .then(html => body.innerHTML = html)
        .catch(() => body.innerHTML = "Gagal memuat data!");
}

function closeDetail() {
    document.getElementById("detailModal").classList.remove("active");
}

// ===== STATUS ORDER =====
function openStatus(data) {
    document.getElementById("orderID").value = data.ID_Order;
    document.getElementById("statusInput").value = data.Status;
    document.getElementById("statusModal").classList.add("active");
}

// Close modal when clicking outside
window.onclick = function(e) {
    if (e.target.classList.contains("modal")) {
        e.target.classList.remove("active");
    }
};

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
    }
});
</script>
<script src="../responsive.js"></script>
</body>
</html>