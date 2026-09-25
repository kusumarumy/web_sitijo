<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit','1024M');
error_reporting(E_ALL);

if (!function_exists('json_response')) {
    function json_response($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

include $_SERVER['DOCUMENT_ROOT'] . "/backend/db/koneksi.php";

$subkelas_param = isset($_GET['subkelas']) ? strtolower(trim($_GET['subkelas'])) : null;

function getStyles()
{
    $basePath = "/assets";
    return [
        "jaringan_drainase" => [
            "inlet" => [
                "type" => "point",
                "marker" => "$basePath/inlet.png",
            ],
            "manhole_drainase" => [
                "type" => "point",
                "marker" => "$basePath/manhole_drainase.png",
            ],
            "sumur_resapan" => [
                "type" => "point",
                "marker" => "$basePath/sumur_resapan.png",
            ],
            "zona_drainase" => [
                "type"   => "line",
                "color"  => "#195dfeff",
                "weight" => 1.5
            ],
        ],
        "jaringan_fiber_optik" => [
            "kabel_fiber_optik" => [
                "type"   => "line",
                "color" => "#000000ff",
                "weight" => 2,
                "opacity" => 0.9
            ],
            "manhole_fiber_optik" => [
                "type" => "point",
                "marker" => "$basePath/manhole_fo.png",
            ],
            "tiang_fiber_optik" => [
                "type" => "point",
                "marker" => "$basePath/tiang_fo.png",
            ],
        ],
    ];
}

function normalizeKey($str)
{
    return strtolower(str_replace(" ", "_", trim($str)));
}

function getStyle($kelas, $subkelas)
{
    $styles = getStyles();
    $kelas = normalizeKey($kelas);
    $subkelas = $subkelas ? normalizeKey($subkelas) : $kelas;
    $styleData = $styles[$kelas][$subkelas] ?? null;
    if (!$styleData) {
        error_log("❌ Style tidak ditemukan: [$kelas][$subkelas]");
        return ["color" => "#444", "weight" => 1];
    }
    if (isset($styleData['styles']) && is_array($styleData['styles'])) {
        $first = $styleData['styles'][0];
        $first['type'] = $styleData['type'] ?? "line";
        return $first;
    }
    return $styleData;
}

function getUtilitas($conn, $table, $fields, $kelas)
{
    $cols = implode(", ", $fields);
    $sql = "SELECT $cols, ST_AsGeoJSON(geometri) AS geojson FROM $table";
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
    $allFeatures = [];
    foreach ($subcollections as $fc) {
        if (!empty($fc['features'])) {
            $allFeatures = array_merge($allFeatures, $fc['features']);
        }
    }
    return [
        "type" => "FeatureCollection",
        "features" => $allFeatures
    ];
}

$response = [
   //jaringan drainase
    "inlet" => getUtilitas($conn, "inlet", ["id", "kelas", "subkelas"], "jaringan_drainase"),
    "manhole_drainase" => getUtilitas($conn, "manhole_drainase", ["id", "kelas", "subkelas"], "jaringan_drainase"),
    "sumur_resapan" => getUtilitas($conn, "sumur_resapan", ["id", "kelas", "subkelas"], "jaringan_drainase"),
    "zona_drainase" => getUtilitas($conn, "zona_drainase", ["id", "kelas", "subkelas", "fungsi", "arah", "tipe", "kondisi", "dimensi", "zona"], "jaringan_drainase"),
    //jaringan fo
    "kabel_fiber_optik" => getUtilitas($conn, "kabel_fo", ["id", "kelas", "subkelas", "provider"], "jaringan_fiber_optik"),
    "manhole_fiber_optik" => getUtilitas($conn, "manhole_fo", ["id", "kelas", "subkelas"], "jaringan_fiber_optik"),
    "tiang_fiber_optik" => getUtilitas($conn, "tiang_fo", ["id", "kelas", "subkelas", "provider"], "jaringan_fiber_optik"),
];
if ($subkelas_param) {
    foreach ($response as $layer => $fc) {
        foreach ($fc['features'] as $feat) {
            if (
                isset($feat['properties']['subkelas']) &&
                strtolower($feat['properties']['subkelas']) === $subkelas_param
            ) {
                $filtered[] = $feat;
            }
        }
    }

    json_response([
        "type" => "FeatureCollection",
        "features" => $filtered ?? []
    ]);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
