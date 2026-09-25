<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit','1024M');
error_reporting(E_ALL);
ob_start();
session_start();
include '../../backend/db/koneksi.php';
include '../../backend/auth/cek_role.php';
include __DIR__ . '/../log/log_akses.php';
logAkses($conn, $_SESSION['user'], "update_data.php", "Memperbaharui atribut data penunjang infrastruktur.");

$captcha_code = strtoupper(substr(md5(rand()), 0, 5));
$_SESSION['captcha_code'] = $captcha_code;

$kelas_subkelas = [
    "atribut_jalan" => ["cermin_jalan", "kamera_pengawas", "lampu_jalan", "lampu_lalin", "rambu_lalin"],
    "halte" => ["titik_halte", "jalur_halte"],
    "infrastruktur_pendukung" => ["hidran", "rantai_pasok", "reklame", "titik_bm"],
    "jaringan_drainase" => ["inlet", "manhole_drainase", "sumur_resapan", "zona_drainase"],
    "jaringan_fiber_optik" => ["kabel_fo", "manhole_fo", "tiang_fo"],
    "jaringan_ipal" => ["manhole_sal", "pipa_glontor", "pipa_induk", "pipa_lateral",],
    "jaringan_jalan" => ["jalan_kota", "jalan_lingkungan", "jalur_pemandu", "jembatan", "trotoar"],
    "jaringan_listrik" => ["tegangan_menengah", "tegangan_rendah", "rumah_kabel", "tiang_listrik", "trafo_listrik"],
    "jaringan_pdam" => ["jaringan_pdam"],
    "sungai" => ["sungai"],
];
$kelas_label_icon = [
    "atribut_jalan"         => "🛤️ Atribut Jalan",
    "halte"                 => "🚌 Halte",
    "infrastruktur_pendukung" => "🏢 Infrastruktur Pendukung",
    "jaringan_drainase"     => "💧 Jaringan Drainase",
    "jaringan_fiber_optik"  => "📡 Jaringan Fiber Optik",
    "jaringan_ipal"         => "🏭 Jaringan Instalasi Pengolahan Air Limbah",
    "jaringan_jalan"        => "🛣️ Jaringan Jalan",
    "jaringan_listrik"      => "🔌 Jaringan Listrik",
    "jaringan_pdam"         => "🚰 Jaringan PDAM",
    "sungai"                => "🏞️ Sungai"
];

$label_subkelas = [
    // ATRIBUT JALAN
    "cermin_jalan" => "Cermin Jalan",
    "kamera_pengawas" => "Kamera Pengawas",
    "lampu_jalan" => "Lampu Jalan",
    "lampu_lalin" => "Lampu Lalu Lintas",
    "rambu_lalin" => "Rambu Lalu Lintas",
    //HALTE
    "titik_halte" => "Titik Halte",
    "jalur_halte" => "Jalur Halte Trans",
    //JARINGAN DRAINASE
    "inlet" => "Inlet",
    "manhole_drainase" => "Manhole Drainase",
    "sumur_resapan" => "Sumur Resapan",
    "zona_drainase" => "Zona Drainase",
    //JARINGAN FIBER OPTIK
    "kabel_fo" => "Kabel Fiber Optik",
    "tiang_fo" => "Tiang Fiber Optik",
    "manhole_fo" => "Manhole Fiber Optik",
    //JARINGAN IPAL
    "manhole_sal" => "Manhole Saluran Air Limbah",
    "pipa_induk" => "Pipa Induk",
    "pipa_glontor" => "Pipa Glontor",
    "pipa_lateral" => "Pipa Lateral",
    // JARINGAN JALAN
    "jalan_kota" => "Jalan Kota",
    "jalan_lingkungan" => "Jalan Lingkungan",
    "jalur_pemandu" => "Jalur Pemandu",
    "jembatan" => "Jembatan",
    "trotoar" => "Trotoar",
    // JARINGAN LISTRIK
    "rumah_kabel" => "Rumah Kabel",
    "tegangan_menengah" => "Jaringan Kabel Listrik Tegangan Menengah",
    "tegangan_rendah" => "Jaringan Kabel Listrik Tegangan Rendah",
    "tiang_listrik" => "Tiang Listrik",
    "trafo_listrik" => "Trafo Listrik",
    // JARINGAN PDAM
    "jaringan_pdam" => "Jaringan PDAM",
    // SUNGAI
    "sungai" => "Sungai",
    // UTILITAS PENDUKUNG
    "hidran" => "Hidran",
    "rantai_pasok" => "Rantai Pasok",
    "reklame" => "Reklame",
    "titik_bm" => "Titik Bench Mark"
];
$kelas = strtolower(trim($_GET['kelas'] ?? ''));
$subkelas = strtolower(trim($_GET['subkelas'] ?? ''));
$data = [];
$fields = [];
$geojsonFeatures = [];
if ($kelas && $subkelas && isset($kelas_subkelas[$kelas]) && in_array($subkelas, $kelas_subkelas[$kelas], true)) {
    $sql = "SELECT *, ST_AsGeoJSON(geometri) AS geojson FROM `$subkelas`";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $fields = array_keys($data[0] ?? []);
        $fields = array_filter($fields, fn($f) => strtolower($f) !== 'geometri' && strtolower($f) !== 'geojson');
        foreach ($data as $row) {
            if (!empty($row['geojson'])) {
                $geometry = json_decode($row['geojson'], true);
                if ($geometry) {
                    $geojsonFeatures[] = [
                        "type" => "Feature",
                        "geometry" => $geometry,
                        "properties" => array_filter($row, fn($k) => !in_array($k, ['geometri', 'geojson']), ARRAY_FILTER_USE_KEY)
                    ];
                }
            }
        }
    }
}
function detectIdField($fields)
{
    foreach ($fields as $f) {
        if (str_starts_with(strtolower($f), 'id')) return $f;
    }
    return $fields[0] ?? 'id';
}
$idField = detectIdField($fields);

?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Update Data <?= htmlspecialchars($subkelas) ?> - Dinas PUPR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" crossorigin="">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        h2 {
            color: #003366;
            margin-top: 10px;
            text-align: center;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            font-size: 30px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        #map {
            height: 450px;
            border-radius: 10px;
            overflow: hidden;
        }

        table th {
            background: #003366;
            color: white;
            text-align: center;
        }

        table td {
            vertical-align: middle;
        }

        tr.selected {
            background-color: #b3d4fc !important;
        }

        .btn-primary {
            background-color: #004aad;
            border-color: #004aad;
        }

        .btn-primary:hover {
            background-color: #003b8e;
        }
    </style>
</head>

<body style="margin-top: 100px; margin-left: 50px;">
    <?php include "../../partials/sidebar.php"; ?>
    <?php include "../../partials/header.php"; ?>
    <div class="container my-4">
        <div class="card shadow p-4">
            <h4 class="mb-4 text-center" style="font-weight: bold; font-size: 30px; margin: 0">🛠️ Pembaruan Data Infrastruktur</h4>
            <div class="card p-4">
                <h6 class="mb-3 fw-semibold">Pilih Kelas dan Subkelas</h6>
                <form method="GET" action="update_data.php" id="form-data" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Kelas</label>
                        <select name="kelas" id="kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas_subkelas as $kelas => $subs): ?>
                                <option value="<?= $kelas ?>">
                                    <?= $kelas_label_icon[$kelas] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Subkelas</label>
                        <select name="subkelas" id="subkelas" class="form-select" required>
                            <option value="">-- Pilih Subkelas --</option>
                            <?php
                            if ($kelas && isset($kelas_subkelas[$kelas])) {
                                foreach ($kelas_subkelas[$kelas] as $sk) {
                                    $selected = ($sk === $subkelas) ? 'selected' : '';
                                    $label = $label_subkelas[$sk] ?? ucfirst(str_replace('_', ' ', $sk));
                                    echo "<option value=\"" . htmlspecialchars($sk) . "\" $selected>" . htmlspecialchars($label) . "</option>";
                                }
                            }

                            ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">🔍 Muat Data</button>
                    </div>
                </form>
            </div>

            <?php if ($data): ?>
                <div class="card mt-4 p-4">
                    <h5 class="mb-3 fw-semibold">Peta Sebaran <?= $label_subkelas[$subkelas] ?? ucfirst(str_replace('_', ' ', $subkelas)) ?></h5>
                    <div id="map" class="mb-4"></div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle text-center" id="dataTable">
                            <thead class="table-primary">
                                <tr>
                                    <?php foreach ($fields as $f): ?>
                                        <th><?= ucfirst(str_replace('_', ' ', $f)) ?></th>
                                    <?php endforeach; ?>
                                    <th style="width: 140px;">Geolokasi</th>
                                    <th style="width: 180px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $row): ?>
                                    <tr data-id="<?= htmlspecialchars($row[$idField]) ?>">
                                        <?php foreach ($fields as $f): ?>
                                            <td><?= htmlspecialchars($row[$f]) ?></td>
                                        <?php endforeach; ?>
                                        <td>
                                            <button class="btn btn-sm btn-outline-success zoom-feature" data-id="<?= $row[$idField] ?>">🔍 Zoom</button>
                                        </td>
                                        <td>
                                                <a href="update_proses.php?subkelas=<?= urlencode($subkelas) ?>&id=<?= urlencode($row[$idField]) ?>" class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>

                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="openDeleteModal('<?= $subkelas ?>', '<?= $row[$idField] ?>', '<?= $idField ?>')">
                                                    <i class="bi bi-trash3"></i> Hapus
                                                </button>
                                            
                                        </td>

                                    </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>

                    <div class="modal fade" id="deleteModal" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" action="hapus_data.php" class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" style="font-weight: bold;">Konfirmasi Hapus Data</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="kelas" id="modalKelas">
<input type="hidden" name="subkelas" id="modalSubkelas">
<input type="hidden" name="id" id="modalId">
<input type="hidden" name="id_field" id="modalIdField">

                                    <div class="mb-3">
                                        <label class="form-label">Nama Penghapus</label>
                                        <input type="text" name="nama_penghapus" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Alasan Penghapusan</label>
                                        <textarea name="alasan_penghapusan" class="form-control" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Bidang Penghapus</label>
                                        <textarea name="unit_penghapus" class="form-control" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Kode Verifikasi</label>
                                        <div style="font-weight:bold; font-size:20px; background:#eee; padding:5px; display:inline-block; margin-bottom:5px;">
                                            <?= $_SESSION['captcha_code'] ?>
                                        </div>
                                        <input type="text" name="captcha_input" class="form-control" required placeholder="Masukkan kode captcha">
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Hapus Data</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php elseif ($subkelas): ?>
                
            <?php endif; ?>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.openDeleteModal = function(subkelas, id, idField) {
    document.getElementById('modalKelas').value = "<?= $kelas ?>";
    document.getElementById('modalSubkelas').value = subkelas;
    document.getElementById('modalId').value = id;
    document.getElementById('modalIdField').value = idField;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
};

        document.addEventListener('DOMContentLoaded', function() {
            const kelasSelect = document.getElementById('kelas');
            const subkelasSelect = document.getElementById('subkelas');
            const kelasSubkelasMap = <?= json_encode($kelas_subkelas) ?>;
            const labelSubkelasMap = <?= json_encode($label_subkelas) ?>;
            kelasSelect.addEventListener('change', () => {
                const kelas = kelasSelect.value;
                subkelasSelect.innerHTML = '<option value="">-- Pilih Subkelas --</option>';
                if (kelas && kelasSubkelasMap[kelas]) {
                    kelasSubkelasMap[kelas].forEach(sk => {
                        const opt = document.createElement('option');
                        opt.value = sk;
                        opt.textContent = labelSubkelasMap[sk] ?? sk.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                        subkelasSelect.appendChild(opt);
                    });
                }
            });

            function loadMap(kelas, subkelas) {
                const mapDiv = document.getElementById('map');
                if (!mapDiv) return;
                mapDiv.innerHTML = '';
                const map = L.map('map');
                const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                const esri = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles © Esri',
                    maxZoom: 21
                });
                L.control.layers({
                    "OpenStreetMap": osm,
                    "ESRI Satellite": esri
                }, {}, {
                    position: 'topright'
                }).addTo(map);

                const defaultStyle = {
                    color: '#004aad',
                    weight: 2,
                    fillOpacity: 0.5
                };
                const highlightStyle = {
                    color: '#ff5c5c',
                    weight: 3,
                    fillOpacity: 0.7
                };
                const idToLayer = {};

                const geojsonData = <?= json_encode([
                                        "type" => "FeatureCollection",
                                        "features" => $geojsonFeatures
                                    ]) ?>;

                if (!geojsonData.features || geojsonData.features.length === 0) {
                    map.setView([-7.797068, 110.370529], 13);
                    return;
                }

                const geojsonLayer = L.geoJSON(geojsonData, {
                    pointToLayer: function(feature, latlng) {
                        return L.circleMarker(latlng, {
                            radius: 6,
                            fillColor: '#ff7800',
                            color: '#000',
                            weight: 1,
                            opacity: 1,
                            fillOpacity: 0.8
                        });
                    },
                    style: function(feature) {
                        if (feature.geometry.type === 'Polygon' || feature.geometry.type === 'MultiPolygon') return {
                            color: '#004aad',
                            weight: 2,
                            fillOpacity: 0.5
                        };
                        if (feature.geometry.type === 'LineString' || feature.geometry.type === 'MultiLineString') return {
                            color: '#004aad',
                            weight: 2
                        };
                        return {};
                    },
                    onEachFeature: (feature, layer) => {
                        const fid = feature.properties.id || feature.properties.id_jalan || feature.properties.id_tm || feature.properties.id_tr || feature.properties.id_reklame || feature.properties.id_trotoar || feature.properties.id_pdam;
                        idToLayer[fid] = layer;
                        layer.bindPopup("<b>ID:</b> " + fid);
                    }
                }).addTo(map);
                if (geojsonLayer.getBounds().isValid()) map.fitBounds(geojsonLayer.getBounds());
                else map.setView([-7.797068, 110.370529], 13);
                const table = document.getElementById('dataTable');
                if (table) {
                    table.querySelectorAll('tbody tr').forEach(tr => {
                        tr.addEventListener('click', () => {
                            const selectedId = tr.getAttribute('data-id');
                            table.querySelectorAll('tr').forEach(r => r.classList.remove('selected'));
                            Object.values(idToLayer).forEach(l => {
                                if (l.setStyle) l.setStyle(defaultStyle);
                            });
                            tr.classList.add('selected');
                            const layer = idToLayer[selectedId];
                            if (layer) {
                                layer.setStyle(highlightStyle);
                                if (layer.getBounds) map.fitBounds(layer.getBounds());
                                else if (layer.getLatLng) map.setView(layer.getLatLng(), 17);
                                layer.openPopup();
                            }
                        });
                    });
                }
                document.querySelectorAll('.zoom-feature').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const fid = btn.getAttribute('data-id');
                        const layer = idToLayer[fid];
                        if (layer) {
                            Object.values(idToLayer).forEach(l => {
                                if (l.setStyle) l.setStyle(defaultStyle);
                            });
                            layer.setStyle(highlightStyle);
                            layer.openPopup();
                            const tbody = table.querySelector('tbody');
                            const row = tbody.querySelector(`tr[data-id='${fid}']`);
                            if (row) {
                                tbody.querySelectorAll('tr').forEach(r => r.classList.remove('selected'));
                                row.classList.add('selected');
                                tbody.insertBefore(row, tbody.firstChild);
                            }
                            if (layer.getBounds) map.fitBounds(layer.getBounds());
                            else if (layer.getLatLng) map.setView(layer.getLatLng(), 17);
                        }
                    });
                });
            }
            <?php if ($subkelas): ?>
                loadMap('<?= $kelas ?>', '<?= $subkelas ?>');
            <?php endif; ?>
        });
       
    </script>
</body>

</html>