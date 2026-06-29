<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "journey_merapi";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    error_log("Database Connection Failed: " . mysqli_connect_error());
    die("Terjadi kesalahan pada server. Silakan coba lagi nanti.");
}

mysqli_set_charset($conn, "utf8");
?>
