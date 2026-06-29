<?php
session_start();
require "../include/db.php";

// Hanya admin yang boleh masuk
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// HAPUS PESAN
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM contact WHERE ID_Contact = $id");
    header("Location: kelola_contact.php");
    exit;
}

// AMBIL DATA
$data = mysqli_query($conn, "SELECT * FROM contact ORDER BY Created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesan Masuk - JourneyMerapi</title>
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
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    white-space: nowrap;
}

.btn-back:hover {
    background: #7f8c8d;
    transform: translateY(-2px);
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
    transform: scale(1.01) translateY(-3px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
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
    vertical-align: top;
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

/* Column widths */
table th:nth-child(1), table td:nth-child(1) { width: 60px; }
table th:nth-child(2), table td:nth-child(2) { width: 180px; }
table th:nth-child(3), table td:nth-child(3) { width: 200px; }
table th:nth-child(4), table td:nth-child(4) { width: auto; }
table th:nth-child(5), table td:nth-child(5) { width: 160px; }
table th:nth-child(6), table td:nth-child(6) { width: 120px; }

/* ==================== MESSAGE DISPLAY ==================== */
.message-text {
    max-width: 400px;
    line-height: 1.6;
    word-wrap: break-word;
    overflow-wrap: break-word;
    text-align: left;
    font-style: normal;
}

/* ==================== ACTION BUTTONS ==================== */
.btn-delete {
    padding: 8px 16px;
    border-radius: 6px;
    background: #e74c3c;
    color: white;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: 0.3s;
    cursor: pointer;
    display: inline-block;
    white-space: nowrap;
}

.btn-delete:hover {
    background: #c0392b;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
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
    margin-bottom: 10px;
}

.empty-state p {
    color: #95a5a6;
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
        <li><a href="dashboard_admin.php">📊 Dashboard</a></li>
        <li><a href="kelola_status_merapi.php">🌋 Status Merapi</a></li>
        <li><a href="kelola_tour.php">🗺️ Tour</a></li>
        <li><a href="kelola_order.php">📋 Orders</a></li>
        <li><a href="kelola_contact.php" class="active">📩 Contact</a></li>
        <li><a href="kelola_user.php">👤 Users</a></li>
    </ul>

    <a href="../auth/logout.php" class="logout-btn">Logout</a>
</aside>

<!-- ==================== MAIN CONTENT ==================== -->
<main class="content">
    <h1>Pesan Masuk</h1>
    <p class="subtitle">Lihat dan kelola pesan yang dikirim oleh pengunjung website</p>

    <div class="button-group">
        <a href="dashboard_admin.php" class="btn-back">← Kembali</a>
    </div>

    <!-- TABLE WRAPPER -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Pesan</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($data) === 0): ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <h3>Belum ada pesan masuk</h3>
                            <p>Pesan dari pengunjung akan muncul di sini</p>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                    <?php while ($row = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= $row['ID_Contact'] ?></td>
                        <td><?= htmlspecialchars($row['Name']) ?></td>
                        <td><?= htmlspecialchars($row['Email']) ?></td>
                        <td>
                            <div class="message-text">
                                <?= htmlspecialchars($row['Message']) ?>
                            </div>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($row['Created_at'])) ?></td>
                        <td>
                            <a 
                               class="btn-delete"
                               onclick="return confirm('⚠️ Hapus pesan dari <?= str_replace("'", "\'", htmlspecialchars($row['Name'])) ?>?')"
                               href="?delete=<?= $row['ID_Contact'] ?>"
                            >Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>
<script src="../responsive.js"></script>
</body>
</html>