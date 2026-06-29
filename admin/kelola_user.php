<?php
session_start();
require "../include/db.php";

// Cek akses admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// DELETE USER
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM users WHERE ID_User = $id");
    header("Location: kelola_user.php");
    exit;
}

// EDIT USER (USERNAME & EMAIL)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id       = intval($_POST["id"]);
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $email    = mysqli_real_escape_string($conn, $_POST["email"]);

    mysqli_query($conn,
        "UPDATE users 
         SET Username='$username', Email='$email'
         WHERE ID_User=$id"
    );

    header("Location: kelola_user.php");
    exit;
}

// Ambil data users
$data = mysqli_query($conn, "SELECT * FROM users ORDER BY ID_User ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Users - JourneyMerapi</title>
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
    transition: all 0.3s;
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
table th:nth-child(1), table td:nth-child(1) { width: 80px; }
table th:nth-child(2), table td:nth-child(2) { width: 250px; }
table th:nth-child(3), table td:nth-child(3) { width: 300px; }
table th:nth-child(4), table td:nth-child(4) { width: 200px; }
table th:nth-child(5), table td:nth-child(5) { width: 200px; }

/* ==================== ACTION BUTTONS ==================== */
.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-edit, .btn-delete {
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    color: white;
    font-size: 13px;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
    transition: 0.3s;
    cursor: pointer;
    border: none;
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
    width: 500px;
    max-width: 90%;
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

.modal-content input {
    width: 100%;
    padding: 12px 15px;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    transition: 0.3s;
}

.modal-content input:focus {
    outline: none;
    border-color: #f0393d;
    box-shadow: 0 0 0 3px rgba(240, 57, 61, 0.1);
}

.btn-save {
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

.btn-save:hover { 
    background: #d6282c;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(240, 57, 61, 0.3);
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
        <li><a href="kelola_contact.php">📩 Contact</a></li>
        <li><a href="kelola_user.php" class="active">👤 Users</a></li>
    </ul>

    <a href="../auth/logout.php" class="logout-btn">Logout</a>
</aside>

<!-- ==================== MAIN CONTENT ==================== -->
<main class="content">

    <h1>Kelola Users</h1>
    <p class="subtitle">Kelola akun pengguna dan administrator sistem</p>

    <div class="button-group">
        <a href="dashboard_admin.php" class="btn-back">← Kembali</a>
    </div>

    <!-- TABLE WRAPPER -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td><?= $row['ID_User'] ?></td>
                    <td><?= htmlspecialchars($row['Username']) ?></td>
                    <td><?= htmlspecialchars($row['Email']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['Created_at'])) ?></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-edit" onclick='editUser(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                Edit
                            </button>
                            <a class="btn-delete" 
                               onclick="return confirm('⚠️ Hapus user ini?')" 
                               href="?delete=<?= $row['ID_User'] ?>">
                                Hapus
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</main>

<!-- ==================== MODAL ==================== -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle">Edit User</h3>

        <form method="POST">
            <input type="hidden" id="userId" name="id">

            <label>Username</label>
            <input type="text" name="username" id="username" required placeholder="Masukkan username">

            <label>Email</label>
            <input type="email" name="email" id="email" required placeholder="Masukkan email">

            <button type="submit" class="btn-save">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
function editUser(data) {
    document.getElementById("modalTitle").innerText = "Edit User";
    document.getElementById("userId").value = data.ID_User;
    document.getElementById("username").value = data.Username;
    document.getElementById("email").value = data.Email;
    document.getElementById("userModal").classList.add("active");
}

// Close modal when clicking outside
window.onclick = function(e) {
    if (e.target.id === "userModal") {
        document.getElementById("userModal").classList.remove("active");
    }
}

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById("userModal").classList.remove("active");
    }
});
</script>
<script src="../responsive.js"></script>
</body>
</html>