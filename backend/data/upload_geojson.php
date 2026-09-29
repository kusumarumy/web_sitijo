<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

header('Content-Type: application/json; charset=utf-8');

session_start();

require_once __DIR__ . '/../db/koneksi.php';

/*
 * upload_geojson.php
 * Versi PostgreSQL + PDO
 *
 * Dipakai oleh tambah_data.php untuk:
 * - menerima file GeoJSON
 * - membaca fitur GeoJSON
 * - memasukkan atribut ke tabel PostgreSQL
 * - memasukkan geometry ke kolom geometri
 * - mencatat proses upload ke log_upload
 */

// Pastikan koneksi PDO tersedia.
if (!isset($conn) || !($conn instanceof PDO)) {
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database PDO tidak ditemukan."
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

try {
    // =========================
    // CEK SESSION
    // =========================
    if (!isset($_SESSION['user'])) {
        echo json_encode([
            "status" => "error",
            "message" => "Sesi pengguna berakhir. Silakan login kembali."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $username = $_SESSION['user']['username'] ?? 'unknown';

    // =========================
    // INPUT
    // =========================
    $kelas = trim($_POST['kelas'] ?? '');
    $subkelas = trim($_POST['subkelas'] ?? '');

    if ($kelas === '' || $subkelas === '') {
        echo json_encode([
            "status" => "error",
            "message" => "Kelas dan subkelas wajib diisi."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Nama tabel harus berasal dari daftar nama tabel yang valid.
    // Ini juga mencegah SQL injection pada identifier tabel.
    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $subkelas)) {
        echo json_encode([
            "status" => "error",
            "message" => "Nama subkelas/tabel tidak valid."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!isset($_FILES['geojson_file'])) {
        echo json_encode([
            "status" => "error",
            "message" => "File GeoJSON belum diunggah."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($_FILES['geojson_file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            "status" => "error",
            "message" => "Upload file gagal. Kode error: " . $_FILES['geojson_file']['error']
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $tmpFile = $_FILES['geojson_file']['tmp_name'];

    if (!is_uploaded_file($tmpFile)) {
        echo json_encode([
            "status" => "error",
            "message" => "File upload tidak valid."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // =========================
    // BACA GEOJSON
    // =========================
    $geojson = file_get_contents($tmpFile);

    if ($geojson === false) {
        echo json_encode([
            "status" => "error",
            "message" => "File GeoJSON tidak dapat dibaca."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $data = json_decode($geojson, true);

    if (
        json_last_error() !== JSON_ERROR_NONE ||
        !is_array($data) ||
        !isset($data['features']) ||
        !is_array($data['features'])
    ) {
        echo json_encode([
            "status" => "error",
            "message" => "Format GeoJSON tidak valid: " . json_last_error_msg()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // =========================
    // CEK TABEL POSTGRESQL
    // =========================
    $tableStmt = $conn->prepare("
        SELECT 1
        FROM information_schema.tables
        WHERE table_schema = 'public'
          AND table_name = :table_name
        LIMIT 1
    ");
    $tableStmt->execute([':table_name' => $subkelas]);

    if (!$tableStmt->fetchColumn()) {
        echo json_encode([
            "status" => "error",
            "message" => "Tabel '$subkelas' tidak ditemukan di database."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // =========================
    // AMBIL KOLOM TABEL
    // =========================
    $columnStmt = $conn->prepare("
        SELECT column_name
        FROM information_schema.columns
        WHERE table_schema = 'public'
          AND table_name = :table_name
        ORDER BY ordinal_position
    ");
    $columnStmt->execute([':table_name' => $subkelas]);

    $colsDB = [];
    while ($row = $columnStmt->fetch()) {
        $colsDB[strtolower($row['column_name'])] = $row['column_name'];
    }

    if (empty($colsDB)) {
        echo json_encode([
            "status" => "error",
            "message" => "Struktur tabel '$subkelas' tidak dapat dibaca."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Pastikan kolom geometri tersedia.
    $geometryColumn = null;

    foreach ($colsDB as $lower => $original) {
        if ($lower === 'geometri' || $lower === 'geometry' || $lower === 'geom') {
            $geometryColumn = $original;
            break;
        }
    }

    if ($geometryColumn === null) {
        echo json_encode([
            "status" => "error",
            "message" => "Kolom geometri tidak ditemukan pada tabel '$subkelas'."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // =========================
    // FUNGSI QUOTE IDENTIFIER
    // =========================
    function quoteIdentifier(string $identifier): string
    {
        return '"' . str_replace('"', '""', $identifier) . '"';
    }

    // =========================
    // KONVERSI GEOJSON -> WKT
    // =========================
    function geoJsonToWKT(array $geometry): string
    {
        $type = strtoupper($geometry['type'] ?? '');
        $coords = $geometry['coordinates'] ?? [];

        if ($type === '' || $coords === []) {
            return '';
        }

        $pointToWKT = function ($point): string {
            if (!is_array($point) || count($point) < 2) {
                throw new InvalidArgumentException("Koordinat titik tidak valid.");
            }

            return $point[0] . ' ' . $point[1];
        };

        switch ($type) {
            case 'POINT':
                return 'POINT(' . $pointToWKT($coords) . ')';

            case 'MULTIPOINT':
                $points = [];
                foreach ($coords as $point) {
                    $points[] = '(' . $pointToWKT($point) . ')';
                }
                return 'MULTIPOINT(' . implode(', ', $points) . ')';

            case 'LINESTRING':
                $points = [];
                foreach ($coords as $point) {
                    $points[] = $pointToWKT($point);
                }
                return 'LINESTRING(' . implode(', ', $points) . ')';

            case 'MULTILINESTRING':
                $lines = [];
                foreach ($coords as $line) {
                    $points = [];
                    foreach ($line as $point) {
                        $points[] = $pointToWKT($point);
                    }
                    $lines[] = '(' . implode(', ', $points) . ')';
                }
                return 'MULTILINESTRING(' . implode(', ', $lines) . ')';

            case 'POLYGON':
                $rings = [];
                foreach ($coords as $ring) {
                    $points = [];
                    foreach ($ring as $point) {
                        $points[] = $pointToWKT($point);
                    }
                    $rings[] = '(' . implode(', ', $points) . ')';
                }
                return 'POLYGON(' . implode(', ', $rings) . ')';

            case 'MULTIPOLYGON':
                $polygons = [];

                foreach ($coords as $polygon) {
                    $rings = [];

                    foreach ($polygon as $ring) {
                        $points = [];

                        foreach ($ring as $point) {
                            $points[] = $pointToWKT($point);
                        }

                        $rings[] = '(' . implode(', ', $points) . ')';
                    }

                    $polygons[] = '(' . implode(', ', $rings) . ')';
                }

                return 'MULTIPOLYGON(' . implode(', ', $polygons) . ')';

            default:
                return '';
        }
    }

    // =========================
    // PROSES INSERT
    // =========================
    $count = 0;
    $geomTypes = [];

    $allowedGeometryTypes = [
        'POINT',
        'MULTIPOINT',
        'LINESTRING',
        'MULTILINESTRING',
        'POLYGON',
        'MULTIPOLYGON'
    ];

    $conn->beginTransaction();

    foreach ($data['features'] as $index => $feature) {

        if (!is_array($feature)) {
            continue;
        }

        if (!isset($feature['geometry']) || !is_array($feature['geometry'])) {
            continue;
        }

        $geometry = $feature['geometry'];
        $props = isset($feature['properties']) && is_array($feature['properties'])
            ? $feature['properties']
            : [];

        $geomType = strtoupper($geometry['type'] ?? '');

        if (!in_array($geomType, $allowedGeometryTypes, true)) {
            continue;
        }

        $wkt = geoJsonToWKT($geometry);

        if ($wkt === '') {
            continue;
        }

        $geomTypes[$geomType] = true;

        $columns = [];
        $placeholders = [];
        $params = [];

        // =========================
        // ATRIBUT
        // =========================
        foreach ($props as $key => $value) {

            $keyLower = strtolower((string)$key);

            // ID dari GeoJSON tidak dimasukkan agar tidak bentrok
            // dengan ID serial/identity database.
            if ($keyLower === 'id') {
                continue;
            }

            if (!isset($colsDB[$keyLower])) {
                continue;
            }

            $dbColumn = $colsDB[$keyLower];

            // Jangan masukkan property ke kolom geometry.
            if ($dbColumn === $geometryColumn) {
                continue;
            }

            $paramName = ':p_' . count($params);

            $columns[] = quoteIdentifier($dbColumn);
            $placeholders[] = $paramName;

            // PostgreSQL PDO dapat menerima null secara langsung.
            if ($value === null) {
                $params[$paramName] = null;
            } elseif (is_bool($value)) {
                $params[$paramName] = $value ? 'true' : 'false';
            } elseif (is_array($value) || is_object($value)) {
                $params[$paramName] = json_encode(
                    $value,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                );
            } else {
                $params[$paramName] = (string)$value;
            }
        }

        // =========================
        // GEOMETRY
        // =========================
        $columns[] = quoteIdentifier($geometryColumn);
        $placeholders[] = 'ST_SetSRID(ST_GeomFromText(:geometry_wkt), 4326)';
        $params[':geometry_wkt'] = $wkt;

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            quoteIdentifier($subkelas),
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $count++;
    }

    // Jika tidak ada fitur yang berhasil dimasukkan.
    if ($count === 0) {
        $conn->rollBack();

        echo json_encode([
            "status" => "error",
            "message" => "Tidak ada fitur yang berhasil diproses dari file GeoJSON."
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // =========================
    // LOG UPLOAD
    // =========================
    $geomList = implode(', ', array_keys($geomTypes));

    /*
     * Log upload dimasukkan dalam transaksi yang sama.
     * Jika tabel log_upload tersedia, proses akan dicatat.
     */
    $logCheck = $conn->query("
        SELECT 1
        FROM information_schema.tables
        WHERE table_schema = 'public'
          AND table_name = 'log_upload'
        LIMIT 1
    ");

    if ($logCheck->fetchColumn()) {
        $logStmt = $conn->prepare("
            INSERT INTO log_upload
                (username, kelas, subkelas, jumlah_data, jenis_geometri, waktu_upload)
            VALUES
                (:username, :kelas, :subkelas, :jumlah_data, :jenis_geometri, NOW())
        ");

        $logStmt->execute([
            ':username' => $username,
            ':kelas' => $kelas,
            ':subkelas' => $subkelas,
            ':jumlah_data' => $count,
            ':jenis_geometri' => $geomList
        ]);
    }

    $conn->commit();

    echo json_encode([
        "status" => "success",
        "message" => "✅ Berhasil mengunggah $count fitur ($geomList) ke tabel '$subkelas'."
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {

    if (isset($conn) && $conn instanceof PDO && $conn->inTransaction()) {
        $conn->rollBack();
    }

    error_log(
        'upload_geojson.php error: ' .
        $e->getMessage() .
        ' in ' . $e->getFile() .
        ':' . $e->getLine()
    );

    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengunggah data: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
