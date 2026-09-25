<?php
$host = "sql113.infinityfree.com";
$user_database = "if0_40452824";
$password_database = "H4uNxEcHPydvCA6";
$database_name = "if0_40452824_web_pupryk";

$conn = new mysqli($host, $user_database, $password_database, $database_name);

if ($conn->connect_error) {
    error_log("Koneksi database gagal: " . $conn->connect_error);
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['status'=>'error','message'=>'Koneksi database gagal']);
    }
    exit;
}
$conn->set_charset("utf8mb4");
?>
