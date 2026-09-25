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
        "infrastruktur_pendukung" => [
            "hidran" => ["type" => "point", "marker" => "$basePath/hidran.png"],
            "rantai_pasok" => ["type" => "point", "marker" => "$basePath/rantai_pasok.png"],
            "reklame" => ["type" => "point", "marker" => "$basePath/reklame.png"],
            "titik_bench_mark" => ["type" => "point", "marker" => "$basePath/titik_bm.png"],
        ],
        "jaringan_ipal" => [
            "manhole_saluran_air_limbah" => [
                "type" => "point",
                "marker" => "$basePath/manhole_ipal.png",
                "size" => [10, 10]
            ],
            "pipa_glontor" => ["type" => "line", "color" => "#19a880ff", "weight" => 2],
            "pipa_induk" => ["type" => "line", "color" => "#5691a8ff", "weight" => 2],
            "pipa_lateral" => ["type" => "line", "color" => "#028ebdff", "weight" => 2],
        ],
        "jaringan_pdam" => [
            "jaringan_pdam" => ["type" => "line", "color" => "rgba(0, 255, 242, 1)", "weight" => 2],
        ],
        "sungai" => [
            "sungai" => ["type" => "line", "color" => "rgba(53, 194, 255, 1)", "weight" => 2],
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
    $all = [];
    foreach ($subcollections as $fc) {
        if (!empty($fc['features'])) {
            $all = array_merge($all, $fc['features']);
        }
    }
    return ["type" => "FeatureCollection", "features" => $all];
}

$response = [
    //infrastruktur pendukung
    "hidran" => getUtilitas($conn, "hidran", ["id", "kelas", "subkelas"], "infrastruktur_pendukung"),
    "rantai_pasok" => getUtilitas($conn, "rantai_pasok", ["id", "kelas", "subkelas", "jenis", "nama", "bentuk", "alamat", "no_telp"], "infrastruktur_pendukung"),
    "reklame" => getUtilitas($conn, "reklame", ["id_reklame", "kelas", "subkelas", "keterangan"], "infrastruktur_pendukung"),
    "titik_bm" => getUtilitas($conn, "titik_bm", ["id", "kelas", "subkelas", "nama", "easting", "northing", "tinggi"], "infrastruktur_pendukung"),
    //jaringan ipal
    "manhole_sal" => getUtilitas($conn, "manhole_sal", ["id", "kelas", "subkelas"], "jaringan_ipal"),
    "pipa_glontor" => getUtilitas($conn, "pipa_glontor", ["id", "kelas", "subkelas"], "jaringan_ipal"),
    "pipa_induk" => getUtilitas($conn, "pipa_induk", ["id", "kelas", "subkelas"], "jaringan_ipal"),
    "pipa_lateral" => getUtilitas($conn, "pipa_lateral", ["id", "kelas", "subkelas"], "jaringan_ipal"),
    //jaringan pdam
    "jaringan_pdam" => getUtilitas($conn, "jaringan_pdam", ["id_pdam", "kelas", "subkelas", "diameter", "jenis"], "jaringan_pdam"),
    //sungai
    "sungai" => getUtilitas($conn, "sungai", ["id", "kelas", "subkelas", "nama", "panjang"], "sungai"),
    
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
