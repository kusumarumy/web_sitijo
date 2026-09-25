<?php
function logAkses($conn, $user= null, $menu = null, $aksi = null, $keterangan = null) {
    if ($menu === null) {
        $menu = basename($_SERVER['PHP_SELF']); 
    }
    if ($aksi === null) {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === "POST") {
            if (strpos($uri, "tambah") !== false || isset($_POST['tambah'])) {
                $aksi = "Tambah Data";
            } elseif (strpos($uri, "edit") !== false || isset($_POST['update'])) {
                $aksi = "Edit Data";
            } elseif (strpos($uri, "hapus") !== false || isset($_POST['delete'])) {
                $aksi = "Hapus Data";
            } else {
                $aksi = "Submit Form";
            }
        } else if ($method === "GET") {
            if (strpos($uri, "detail") !== false) {
                $aksi = "Lihat Detail";
            } elseif (strpos($uri, "edit") !== false) {
                $aksi = "Buka Halaman Edit";
            } elseif (strpos($uri, "tambah") !== false) {
                $aksi = "Buka Halaman Tambah";
            } else {
                $aksi = "Lihat Halaman";
            }
        }
    }
    if ($keterangan === null) {
        $url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $file = basename($_SERVER['PHP_SELF']);
        $method = $_SERVER['REQUEST_METHOD'];
        $input = $method === "POST" ? $_POST : $_GET;
        foreach (['password', 'pass', 'pwd'] as $key) {
            if (isset($input[$key])) unset($input[$key]);
        }
        $keterangan = "File: $file | Method: $method | URL: $url | Input: " . json_encode($input);
    }
    $stmt = $conn->prepare("INSERT INTO log_akses 
        (username, unit, menu, aksi, keterangan, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $username = $user['username'] ?? 'guest';
    $unit = $user['unit'] ?? 'guest';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $stmt->bind_param("sssssss", $username, $unit, $menu, $aksi, $keterangan, $ip, $agent);
    $stmt->execute();
    $stmt->close();
}
?>