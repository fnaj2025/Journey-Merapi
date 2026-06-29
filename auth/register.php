<?php
session_start();
require "../include/db.php";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($username === "" || $email === "" || $password === "") {
        echo "<script>alert('Semua field harus diisi.'); window.history.back();</script>";
        exit;
    }

    $check = $conn->prepare("SELECT * FROM users WHERE Username = ? OR Email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username atau email sudah digunakan!'); window.history.back();</script>";
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $insert = $conn->prepare("INSERT INTO users (Username, Email, Password) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $username, $email, $hashed);

    if ($insert->execute()) {
        // REDIRECT KE INDEX DENGAN AUTO OPEN LOGIN MODAL
        echo "<script>
            alert('Registrasi berhasil! Silakan login dengan akun Anda.');
            
            // Tutup modal register dan buka modal login
            window.location.href = '../index.php?action=openLogin';
        </script>";
        exit;
    } else {
        echo "<script>alert('Terjadi kesalahan saat registrasi.'); window.history.back();</script>";
        exit;
    }
}
?>