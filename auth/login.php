<?php
session_start();
require "../include/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    /* ==========================
        LOGIN ADMIN
    =========================== */
    $sql = "SELECT * FROM admin WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $admin = $res->fetch_assoc();

        if (password_verify($password, $admin["Password"])) {
            $_SESSION["user_id"]  = $admin["ID_admin"];
            $_SESSION["username"] = $admin["Username"];
            $_SESSION["role"]     = "admin";

            header("Location: ../admin/dashboard_admin.php");
            exit;
        }
    }

    /* ==========================
        LOGIN USER BIASA
    =========================== */
    $sql = "SELECT * FROM users WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        if (password_verify($password, $user["Password"])) {
            $_SESSION["user_id"]  = $user["ID_User"];
            $_SESSION["username"] = $user["Username"];
            $_SESSION["role"]     = "user";

            // CEK APAKAH ADA PENDING ORDER DARI TOUR
            echo "
            <script>
                // Cek apakah ada tour yang pending
                const pendingTour = sessionStorage.getItem('pendingTourOrder');
                
                if (pendingTour) {
                    try {
                        const tour = JSON.parse(pendingTour);
                        const now = Date.now();
                        
                        // Validasi: hanya redirect jika < 10 menit
                        if (now - tour.timestamp < 10 * 60 * 1000) {
                            // Hapus dari sessionStorage
                            sessionStorage.removeItem('pendingTourOrder');
                            
                            // Redirect ke order dengan tour ID
                            window.location.href = '../order.php?tour=' + tour.id;
                        } else {
                            // Expired, hapus dan redirect ke index
                            sessionStorage.removeItem('pendingTourOrder');
                            window.location.href = '../index.php';
                        }
                    } catch (e) {
                        window.location.href = '../index.php';
                    }
                } else {
                    // Tidak ada pending order, redirect ke index
                    window.location.href = '../index.php';
                }
            </script>
            ";
            exit;
        }
    }

    /* ==========================
        GAGAL LOGIN
    =========================== */
    echo "<script>
        alert('Username atau Password salah!');
        window.history.back();
    </script>";
    exit;
}
?>