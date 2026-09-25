<?php
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../db/koneksi.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Sesi pengguna berakhir. Silakan login kembali."]);
    exit;
}

$username = $_SESSION['user']['username'] ?? 'unknown';
$kelas = $_POST['kelas'] ?? '';
$subkelas = $_POST['subkelas'] ?? '';

if (!$kelas || !$subkelas) {
    echo json_encode(["status" => "error", "message" => "Kelas dan subkelas wajib diisi."]);
    exit;
}

if (empty($_FILES['geojson_file']['tmp_name'])) {
    echo json_encode(["status" => "error", "message" => "File GeoJSON belum diunggah."]);
    exit;
}

$geojson = file_get_contents($_FILES['geojson_file']['tmp_name']);
$data = json_decode($geojson, true);

if (!$data || !isset($data['features'])) {
    echo json_encode(["status" => "error", "message" => "Format GeoJSON tidak valid."]);
    exit;
}

$db = $conn ?? $mysqli ?? null;
if (!$db) {
    echo json_encode(["status" => "error", "message" => "Koneksi database tidak ditemukan."]);
    exit;
}

$tabel = $db->real_escape_string($subkelas);

$colsDB = [];
$result = $db->query("SHOW COLUMNS FROM `$tabel`");
while ($r = $result->fetch_assoc()) {
    $colsDB[] = strtolower($r['Field']);
}

$count = 0;
$geomTypes = [];
function geoJsonToWKT($geometry) {
    $type = strtoupper($geometry['type'] ?? '');
    $coords = $geometry['coordinates'] ?? [];

    if (!$type || !$coords) return '';

    switch($type) {
        case 'POINT':
            return "POINT({$coords[0]} {$coords[1]})";
        case 'MULTIPOINT':
            $pts = array_map(fn($c)=>"{$c[0]} {$c[1]}", $coords);
            return "MULTIPOINT(" . implode(", ", $pts) . ")";
        case 'LINESTRING':
            $pts = array_map(fn($c)=>"{$c[0]} {$c[1]}", $coords);
            return "LINESTRING(" . implode(", ", $pts) . ")";
        case 'MULTILINESTRING':
            $lines = array_map(fn($line)=>"(" . implode(", ", array_map(fn($c)=>"{$c[0]} {$c[1]}", $line)) . ")", $coords);
            return "MULTILINESTRING(" . implode(", ", $lines) . ")";
        case 'POLYGON':
            $rings = array_map(fn($ring)=>"(" . implode(", ", array_map(fn($c)=>"{$c[0]} {$c[1]}", $ring)) . ")", $coords);
            return "POLYGON(" . implode(", ", $rings) . ")";
        case 'MULTIPOLYGON':
            $polys = array_map(fn($poly)=>"(" . implode(", ", array_map(fn($ring)=>"(" . implode(", ", array_map(fn($c)=>"{$c[0]} {$c[1]}", $ring)) . ")", $poly)) . ")", $coords);
            return "MULTIPOLYGON(" . implode(", ", $polys) . ")";
        default:
            return '';
    }
}

foreach ($data['features'] as $f) {
    if (!isset($f['geometry'])) continue;
    $geometry = $f['geometry'];
    $props = $f['properties'] ?? [];
    $geomJson = json_encode($geometry);
    $geomType = strtoupper($geometry['type'] ?? "UNKNOWN");
    $geomTypes[$geomType] = true;
    $allowed = ["POINT", "MULTIPOINT", "LINESTRING", "MULTILINESTRING", "POLYGON", "MULTIPOLYGON"];
    if (!in_array($geomType, $allowed)) continue;
    $cols = [];
    $vals = [];
    foreach ($props as $k => $v) {
        $kLower = strtolower($k);
if ($kLower === 'id') continue;
        if (in_array($kLower, $colsDB)) {
            $cols[] = "`$kLower`";
            $vals[] = "'" . $db->real_escape_string($v) . "'";
        }
    }

    $wkt = geoJsonToWKT($geometry);
if ($wkt) {
    $cols[] = "geometri";
    $vals[] = "ST_GeomFromText('" . $db->real_escape_string($wkt) . "')";
} else {
    continue; 
}


    $sql = "INSERT INTO `$tabel` (" . implode(",", $cols) . ") VALUES (" . implode(",", $vals) . ")";
    if ($db->query($sql)) $count++;
}

$geomList = implode(", ", array_keys($geomTypes));

$stmt = $db->prepare("INSERT INTO log_upload (username, kelas, subkelas, jumlah_data, jenis_geometri, waktu_upload)
                     VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("sssis", $username, $kelas, $subkelas, $count, $geomList);
$stmt->execute();

echo json_encode([
    "status" => "success",
    "message" => "✅ Berhasil mengunggah $count fitur ($geomList) ke tabel '$subkelas'."
]);
?>
