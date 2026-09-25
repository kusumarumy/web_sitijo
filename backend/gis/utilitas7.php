<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit','1024M');
error_reporting(E_ALL);

include $_SERVER['DOCUMENT_ROOT'] . "/backend/db/koneksi.php";

if (!function_exists('json_response')) {
    function json_response($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

function normalizeKey($str)
{
    return strtolower(str_replace(" ", "_", trim($str)));
}

function getStyles()
{
    $basePath = "/assets";
    return [
        "jaringan_jalan" => [
            "trotoar" => ["type" => "polygon", "color" => "grey"],
        ],
    ];
}

function getStyle($kelas, $subkelas)
{
    $styles = getStyles();
    $kelas = normalizeKey($kelas);
    $subkelas = normalizeKey($subkelas);
    return $styles[$kelas][$subkelas] ?? ["color" => "#666", "weight" => 1];
}

function getUtilitas($conn, $table, $fields, $kelas)
{
    $cols = implode(", ", $fields);
    $sql = "SELECT $cols, ST_AsGeoJSON(geometri) AS geojson FROM $table WHERE id_trotoar BETWEEN 1 AND 887";
    $result = $conn->query($sql);
    $features = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $subkelas = strtolower(trim($row['subkelas'] ?? 'lainnya'));
            $style = getStyle($kelas, $subkelas);

            $geometry = null;
            if (!empty($row['geojson']) && is_string($row['geojson'])) {
                $geometry = json_decode($row['geojson']);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("⚠️ Gagal decode GeoJSON ($table): " . json_last_error_msg());
                    $geometry = null;
                }
            }
            unset($row['geojson']);
            if ($geometry && isset($geometry->type)) {
                if ($geometry->type === "GeometryCollection") {
                    if (
                        !isset($geometry->geometries) ||
                        !is_array($geometry->geometries) ||
                        count($geometry->geometries) === 0
                    ) {
                        continue;
                    }
                }
                $allowedTypes = ["Point", "MultiPoint", "LineString", "MultiLineString", "Polygon", "MultiPolygon", "GeometryCollection"];
                if (!in_array($geometry->type, $allowedTypes)) {
                    error_log("⚠️ Geometry type tidak dikenal ($table): " . $geometry->type);
                    continue;
                }
                $features[] = [
                    "type" => "Feature",
                    "geometry" => $geometry,
                    "properties" => $row,
                    "style" => $style
                ];
            }
        }
    }
    return [
        "type" => "FeatureCollection",
        "features" => $features
    ];
}

function gabungPerKelas($subcollections)
{
    $all = [];
    foreach ($subcollections as $fc) {
        if (!empty($fc['features'])) {
            $all = array_merge($all, $fc['features']);
        }
    }
    return ["type" => "FeatureCollection", "features" => $all];
}

$response = [
    //jaringan jalan
    "trotoar" => getUtilitas($conn, "trotoar", ["id_trotoar", "nama_jalan", "lebar_tro", "kelurahan", "kemantren", "kelas", "subkelas"], "jaringan_jalan"),
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
