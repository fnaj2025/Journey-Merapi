<?php
session_start();
require "../include/db.php";

// Cek akses admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// DELETE TOUR
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM tour WHERE ID_Tour = $id");
    header("Location: kelola_tour.php");
    exit;
}

// TAMBAH / EDIT TOUR
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id         = $_POST["id"] ?? null;
    $nama       = mysqli_real_escape_string($conn, $_POST["nama"]);
    $harga      = mysqli_real_escape_string($conn, $_POST["harga"]);
    $rating     = mysqli_real_escape_string($conn, $_POST["rating"]);
    $gambar     = mysqli_real_escape_string($conn, $_POST["gambar"]);
    $deskripsi  = mysqli_real_escape_string($conn, $_POST["deskripsi"]);

    if (empty($id)) {
        mysqli_query($conn,
            "INSERT INTO tour (Nama, Harga_mulai, Rating, Gambar, Deskripsi)
             VALUES ('$nama', '$harga', '$rating', '$gambar', '$deskripsi')"
        );
    } else {
        mysqli_query($conn,
            "UPDATE tour SET 
                Nama='$nama',
                Harga_mulai='$harga',
                Rating='$rating',
                Gambar='$gambar',
                Deskripsi='$deskripsi'
             WHERE ID_Tour=$id"
        );
    }

    header("Location: kelola_tour.php");
    exit;
}

// Ambil data tour
$data = mysqli_query($conn, "SELECT * FROM tour ORDER BY ID_Tour ASC");

// Cek query error
if (!$data) {
    die("Error query: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tour - JourneyMerapi Admin</title>
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

/* ==================== BUTTONS ==================== */
.button-group {
    display: flex;
    gap: 15px;
    margin: 25px 0;
    flex-wrap: wrap;
}

.btn-add, .btn-back {
    display: inline-block;
    padding: 13px 26px;
    background: #f0393d;
    color: #fff;
    border-radius: 10px;
    text-decoration: none;
    font-size: 16px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    letter-spacing: 0.3px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    white-space: nowrap;
}

.btn-add:hover, .btn-back:hover {
    background: #d6282c;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(240, 57, 61, 0.3);
}

.btn-back {
    background: #95a5a6;
}

.btn-back:hover {
    background: #7f8c8d;
    box-shadow: 0 4px 12px rgba(149, 165, 166, 0.3);
}

/* ==================== TABLE WRAPPER ==================== */
.table-wrapper {
    overflow: visible;
    margin-top: 20px;
    margin-bottom: 30px;
    border-radius: 12px;
    background: white;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.table-wrapper:hover {
    transform: scale(1.02) translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15),
                0 8px 20px rgba(0, 0, 0, 0.1);
}

/* ==================== TABLE ==================== */
table {
    width: 100%;
    min-width: 1000px;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
}

thead {
    background: linear-gradient(135deg, #f0393d 0%, #d6282c 100%);
}

th {
    padding: 16px;
    color: white;
    font-weight: 600;
    text-align: left;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

td {
    padding: 14px 16px;
    border-bottom: 1px solid #ecf0f1;
    font-size: 14px;
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

/* Image dalam table */
td img {
    width: 120px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: 0.3s;
}

td img:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Fixed column widths */
table th:nth-child(1), table td:nth-child(1) { width: 60px; }
table th:nth-child(2), table td:nth-child(2) { width: 150px; }
table th:nth-child(3), table td:nth-child(3) { width: 280px; }
table th:nth-child(4), table td:nth-child(4) { width: 150px; }
table th:nth-child(5), table td:nth-child(5) { width: 100px; }
table th:nth-child(6), table td:nth-child(6) { width: 200px; }

/* ==================== RATING STARS ==================== */
.rating {
    color: #322d24ad;
    font-size: 16px;
    font-weight: 600;
}

.rating::before {
    content: " ";
}

/* ==================== PRICE ==================== */
.price {
    font-weight: 700;
    color: #1e1f1eff;
    font-size: 15px;
}

/* ==================== ACTION BUTTONS ==================== */
.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: nowrap;
}

.btn-edit, .btn-delete {
    padding: 9px 18px;
    border-radius: 6px;
    text-decoration: none;
    color: white;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 0.3px;
    transition: 0.3s;
    cursor: pointer;
    white-space: nowrap;
}

.btn-edit {
    background: #3498db;
}

.btn-edit:hover { 
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
    width: 550px;
    max-width: 90%;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    animation: slideDown 0.3s;
    max-height: 90vh;
    overflow-y: auto;
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

.modal-content input,
.modal-content textarea {
    width: 100%;
    padding: 12px 15px;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    transition: 0.3s;
}

.modal-content input:focus,
.modal-content textarea:focus {
    outline: none;
    border-color: #f0393d;
    box-shadow: 0 0 0 3px rgba(240, 57, 61, 0.1);
}

.modal-content textarea {
    min-height: 120px;
    resize: vertical;
}

.btn-save {
    background: #f0393d;
    color: white;
    padding: 14px;
    border: none;
    width: 100%;
    border-radius: 10px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    letter-spacing: 0.3px;
    margin-top: 20px;
    transition: 0.3s;
    font-family: 'Poppins', sans-serif;
}

.btn-save:hover { 
    background: #d6282c;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(240, 57, 61, 0.3);
}

/* ==================== EMPTY STATE ==================== */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #95a5a6;
}

.empty-state h3 {
    font-size: 20px;
    color: #7f8c8d;
}

/* ==================== RESPONSIVE SAFEGUARD ==================== */
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
        <li><a href="dashboard_admin.php">📊 Dashboard</a></li>
        <li><a href="kelola_status_merapi.php">🌋 Status Merapi</a></li>
        <li><a href="kelola_tour.php" class="active">🗺️ Tour</a></li>
        <li><a href="kelola_order.php">📝 Orders</a></li>
        <li><a href="kelola_contact.php">📩 Contact</a></li>
        <li><a href="kelola_user.php">👤 Users</a></li>
    </ul>

    <a href="../auth/logout.php" class="logout-btn">Logout</a>
</aside>

<!-- ==================== MAIN CONTENT ==================== -->
<main class="content">

    <h1>Kelola Paket Tour</h1>
    <p style="color: #7f8c8d; margin-bottom: 20px;">Kelola dan update paket tour wisata Merapi</p>

    <div class="button-group">
        <a href="dashboard_admin.php" class="btn-back">← Kembali</a>
        <button class="btn-add" onclick="openModal()">+ Tambah Tour</button>
    </div>

    <!-- TABLE WRAPPER -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Nama Tour</th>
                    <th>Harga Mulai</th>
                    <th>Rating</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($data) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($data)): ?>
                        <?php 
                            $gambar = $row['Gambar'];

                            if (preg_match('/^https?:\/\//', $gambar)) {
                                $imgSrc = $gambar;
                            } else {
                                $imgSrc = "/journey_merapi/" . $gambar;
                                $localPath = $_SERVER['DOCUMENT_ROOT'] . "/journey_merapi/" . $gambar;

                                if (!file_exists($localPath)) {
                                    $imgSrc = "/journey_merapi/img/no-image.png";
                                }
                            }
                        ?>
                        <tr>
                            <td><?= $row['ID_Tour'] ?></td>
                            <td><img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($row['Nama']) ?>"></td>
                            <td><?= htmlspecialchars($row['Nama']) ?></td>
                            <td><span class="price">IDR <?= number_format($row['Harga_mulai'], 0, ",", ".") ?></span></td>
                            <td><span class="rating"><?= number_format($row['Rating'], 1) ?></span></td>
                            <td>
                                <div class="action-buttons">
                                    <a class="btn-edit" onclick='editTour(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                        Edit
                                    </a>
                                    <a class="btn-delete" 
                                       href="?delete=<?= $row['ID_Tour'] ?>"
                                       onclick="return confirm('⚠️ Yakin hapus tour ini?')">
                                        Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">
                            <div class="empty-state">
                                <h3>Belum ada data tour</h3>
                                <p>Klik "Tambah Tour" untuk menambahkan paket tour baru</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<!-- ==================== MODAL FORM ==================== -->
<div id="tourModal" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle">Tambah Tour Baru</h3>

        <form method="POST">
            <input type="hidden" id="tourId" name="id">

            <label>Nama Tour</label>
            <input type="text" name="nama" id="nama" placeholder="Contoh: Jeep Lava Tour Merapi" required>

            <label>Harga Mulai (IDR)</label>
            <input type="number" name="harga" id="harga" placeholder="Contoh: 350000" required>

            <label>Rating (0 - 5)</label>
            <input type="number" step="0.1" min="0" max="5" name="rating" id="rating" placeholder="Contoh: 4.8" required>

            <label>URL Gambar</label>
            <input type="text" name="gambar" id="gambar" placeholder="img/jeep-tour.jpg atau https://..." required>

            <label>Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" placeholder="Jelaskan detail paket tour..." required></textarea>

            <button type="submit" class="btn-save">Simpan</button>
        </form>
    </div>
</div>

<!-- ==================== JAVASCRIPT ==================== -->
<script>
// Buka modal untuk tambah tour baru
function openModal() {
    document.getElementById("modalTitle").innerText = "Tambah Tour Baru";
    document.getElementById("tourId").value = "";
    document.getElementById("nama").value = "";
    document.getElementById("harga").value = "";
    document.getElementById("rating").value = "";
    document.getElementById("gambar").value = "";
    document.getElementById("deskripsi").value = "";

    document.getElementById("tourModal").classList.add("active");
}

// Edit tour yang sudah ada
function editTour(data) {
    document.getElementById("modalTitle").innerText = "Edit Tour";

    document.getElementById("tourId").value = data.ID_Tour;
    document.getElementById("nama").value = data.Nama;
    document.getElementById("harga").value = data.Harga_mulai;
    document.getElementById("rating").value = data.Rating;
    document.getElementById("gambar").value = data.Gambar;
    document.getElementById("deskripsi").value = data.Deskripsi;

    document.getElementById("tourModal").classList.add("active");
}

// Close modal saat klik di luar
window.onclick = function(event) {
    const modal = document.getElementById("tourModal");
    if (event.target === modal) {
        modal.classList.remove("active");
    }
}

// Close modal dengan tombol ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.getElementById("tourModal").classList.remove("active");
    }
});
</script>
<script src="../responsive.js"></script>
</body>
</html>