<?php
session_start();
require "include/db.php";

// CEK LOGIN
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Silakan login terlebih dahulu untuk mengirim pesan."
    ]);
    exit;
}

// Pastikan request POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request!");
}

$name    = mysqli_real_escape_string($conn, $_POST["name"]);
$email   = mysqli_real_escape_string($conn, $_POST["email"]);
$message = mysqli_real_escape_string($conn, $_POST["message"]);

$user_id = intval($_SESSION['user_id']);

// Simpan ke database
$stmt = $conn->prepare("
    INSERT INTO contact (User_ID, Name, Email, Message)
    VALUES (?, ?, ?, ?)
");

$stmt->bind_param("isss", $user_id, $name, $email, $message);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Pesan berhasil dikirim!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal menyimpan pesan ke database"
    ]);
}

$stmt->close();
$conn->close();
?>