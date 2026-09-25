<?php

$databaseUrl = getenv('DATABASE_URL');

if (!$databaseUrl) {
    error_log("DATABASE_URL belum dikonfigurasi");
    exit;
}

$parts = parse_url($databaseUrl);

if ($parts === false) {
    error_log("DATABASE_URL tidak valid");
    exit;
}

$host = $parts['host'];
$port = $parts['port'] ?? 5432;
$dbname = ltrim($parts['path'] ?? '/postgres', '/');
$username = urldecode($parts['user'] ?? '');
$password = urldecode($parts['pass'] ?? '');

try {

    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";

    $conn = new PDO(
        $dsn,
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Transaction Pooler Supabase tidak mendukung
            // native prepared statements.
            PDO::ATTR_EMULATE_PREPARES => true,
        ]
    );

} catch (PDOException $e) {

    error_log("Koneksi database Supabase gagal: " . $e->getMessage());

    if (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        header('Content-Type: application/json');

        echo json_encode([
            'status' => 'error',
            'message' => 'Koneksi database gagal'
        ]);
    }

    exit;
}
