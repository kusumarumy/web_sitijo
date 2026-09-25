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
        "atribut_jalan" => [
            "cermin_jalan" => [
                "type" => "point",
                "marker" => "$basePath/cermin_jalan.png",
            ],
            "lampu_jalan" => [
                "type" => "point",
                "marker" => "$basePath/lampu_jalan.png",
            ],
            "lampu_lalu_lintas" => [
                "type" => "point",
                "marker" => "$basePath/lampu_lalin.png",
            ],
            "kamera_pengawas" => [
                "type" => "point",
                "marker" => "$basePath/kamera_pengawas.png",
            ],
            "rambu_lalu_lintas" => [
                "type" => "point",
                "marker" => "$basePath/rambu_lalin.png",
            ],
        ],
        "jaringan_listrik" => [
            "jaringan_kabel_listrik_tegangan_menengah" => [
                "type" => "line", 
                "color" => "#8d8b08ff", 
                "weight" => 2],
            "jaringan_kabel_listrik_tegangan_rendah" => [
                "type" => "line", 
                "color" => "#adb162", 
                "weight" => 2],
            "rumah_kabel" => [
                "type" => "point", 
                "marker" => "$basePath/rumah_kabel.png", 
                "size" => [20, 20]],
            "tiang_listrik" => [
                "type" => "point", 
                "marker" => "$basePath/tiang_listrik.png", 
                "size" => [20, 20]],
            "trafo_listrik" => [
                "type" => "point", 
                "marker" => "$basePath/trafo_listrik.png", 
                "size" => [20, 20]],
        ],
        "halte" => [
            "titik_halte" => [
                "type"  => "point",
                "marker" => "$basePath/titik_halte.png",
            ],
            "jalur_halte_trans" => [
                "type"  => "multilinestring",
                "color" => "rgba(251, 114, 29, 0.8)",
                "weight" => 2.5,
                "opacity" => 0.85,
                "dashArray" => "6 3",
            ]
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
    //atribut jalan
    "cermin_jalan" => getUtilitas($conn, "cermin_jalan", ["id", "kelas", "subkelas"], "atribut_jalan"),
    "kamera_pengawas" => getUtilitas($conn, "kamera_pengawas", ["id", "kelas", "subkelas"], "atribut_jalan"),
    "lampu_jalan" => getUtilitas($conn, "lampu_jalan", ["id", "kelas", "subkelas", "kode", "kondisi", "pondasi", "daya_lampu", "teknologi"], "atribut_jalan"),
    "lampu_lalin" => getUtilitas($conn, "lampu_lalin", ["id", "kelas", "subkelas"], "atribut_jalan"),
    "rambu_lalin" => getUtilitas($conn, "rambu_lalin", ["id", "kelas", "subkelas"], "atribut_jalan"),
    //halte
    "titik_halte" => getUtilitas($conn, "titik_halte", ["id", "kelas", "subkelas", "nama"], "halte"),
    "jalur_halte" => getUtilitas($conn, "jalur_halte", ["id", "kelas", "subkelas", "nama"], "halte"),
    //jaringan listrik
    "tegangan_menengah" => getUtilitas($conn, "tegangan_menengah", ["id_tm", "kelas", "subkelas", "klasifikas", "posisi_fas", "ukuran_kaw", "nama_gi"], "jaringan_listrik"),
    "tegangan_rendah" => getUtilitas($conn, "tegangan_rendah", ["id_tr", "kelas", "subkelas", "klasifikas", "posisi_fas", "ukuran_kaw", "nama_gi", "bahan_kawa", "deskripsi"], "jaringan_listrik"),
    "rumah_kabel" => getUtilitas($conn, "rumah_kabel", ["id", "kelas", "subkelas"], "jaringan_listrik"),
    "tiang_listrik" => getUtilitas($conn, "tiang_listrik", ["id", "kelas", "subkelas"], "jaringan_listrik"),
    "trafo_listrik" => getUtilitas($conn, "trafo_listrik", ["id", "kelas", "subkelas"], "jaringan_listrik"),
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
