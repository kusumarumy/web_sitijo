<?php

function logAkses(
    $conn,
    $user = null,
    $menu = null,
    $aksi = null,
    $keterangan = null
) {
    if ($menu === null) {
        $menu = basename($_SERVER['PHP_SELF']);
    }

    if ($aksi === null) {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === "POST") {
            if (
                strpos($uri, "tambah") !== false ||
                isset($_POST['tambah'])
            ) {
                $aksi = "Tambah Data";
            } elseif (
                strpos($uri, "edit") !== false ||
                isset($_POST['update'])
            ) {
                $aksi = "Edit Data";
            } elseif (
                strpos($uri, "hapus") !== false ||
                isset($_POST['delete'])
            ) {
                $aksi = "Hapus Data";
            } else {
                $aksi = "Submit Form";
            }
        } elseif ($method === "GET") {
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
        $scheme = $_SERVER['REQUEST_SCHEME']
            ?? (
                (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                    ? 'https'
                    : 'http'
            );

        $host = $_SERVER['HTTP_HOST'] ?? '';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';

        $url = $scheme . "://" . $host . $requestUri;

        $file = basename($_SERVER['PHP_SELF'] ?? '');
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        $input = $method === "POST" ? $_POST : $_GET;

        // Jangan simpan password ke log
        foreach (['password', 'pass', 'pwd'] as $key) {
            if (isset($input[$key])) {
                unset($input[$key]);
            }
        }

        $keterangan =
            "File: $file | Method: $method | URL: $url | Input: "
            . json_encode($input, JSON_UNESCAPED_UNICODE);
    }

    $stmt = $conn->prepare("
        INSERT INTO log_akses
        (
            username,
            unit,
            menu,
            aksi,
            keterangan,
            ip_address,
            user_agent
        )
        VALUES
        (
            :username,
            :unit,
            :menu,
            :aksi,
            :keterangan,
            :ip_address,
            :user_agent
        )
    ");

    $username = $user['username'] ?? 'guest';
    $unit = $user['unit'] ?? 'guest';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $stmt->execute([
        ':username'   => $username,
        ':unit'       => $unit,
        ':menu'       => $menu,
        ':aksi'       => $aksi,
        ':keterangan' => $keterangan,
        ':ip_address' => $ip,
        ':user_agent' => $agent
    ]);
}
?>
