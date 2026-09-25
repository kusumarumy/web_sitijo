<?php

require_once __DIR__ . '/koneksi.php';

try {

    $stmt = $conn->query("
        SELECT
            current_database() AS database_name,
            current_user AS database_user
    ");

    $data = $stmt->fetch();

    header('Content-Type: application/json');

    echo json_encode([
        'status' => 'success',
        'message' => 'Supabase berhasil terhubung',
        'database' => $data['database_name'],
        'user' => $data['database_user']
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    header('Content-Type: application/json');

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
