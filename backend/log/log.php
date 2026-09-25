<?php
function logAkses($conn, $username, $unit, $menu, $aksi = null, $keterangan = null) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO log_akses (username, unit, menu, aksi, keterangan, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $username, $unit, $menu, $aksi, $keterangan, $ip, $ua);
    $stmt->execute();
    $stmt->close();
}
