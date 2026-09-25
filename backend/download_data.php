<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '1024M');
session_start();   
require_once "../../backend/db/koneksi.php";
include __DIR__ . '/../log/log_akses.php';
$user = $_SESSION['user'] ?? ['username'=>'guest', 'unit'=>'guest'];
logAkses($conn, $user, "download_data.php", "Mengunduh data infrastruktur.");

$mapSubkelasToTable = [
    "cermin jalan" => "cermin_jalan",
    "kamera pengawas" => "kamera_pengawas",
    "lampu jalan" => "lampu_jalan",
    "lampu lalu lintas" => "lampu_lalin",
    "rambu lalu lintas" => "rambu_lalin",    
    "jalur halte trans" => "jalur_halte",
    "titik halte" => "titik_halte",
    "inlet" => "inlet",
    "manhole drainase" => "manhole_drainase",
    "sumur resapan" => "sumur_resapan",
    "zona drainase" => "zona_drainase",
    "kabel fiber optik" => "kabel_fo",
    "manhole fiber optik" => "manhole_fo",
    "tiang fiber optik" => "tiang_fo",
    "manhole saluran air limbah" => "manhole_sal",
    "pipa glontor" => "pipa_glontor",
    "pipa induk" => "pipa_induk",
    "pipa lateral" => "pipa_lateral",
    "jalan kota" => "jalan_kota",
    "jalan lingkungan" => "jalan_lingkungan",
    "jalur pemandu" => "jalur_pemandu",
    "jembatan" => "jembatan",
    "trotoar" => "trotoar",
    "jaringan pdam" => "jaringan_pdam",
    "jaringan kabel listrik tegangan menengah" => "tegangan_menengah",
    "jaringan kabel listrik tegangan rendah" => "tegangan_rendah",
    "rumah kabel" => "rumah_kabel",
    "tiang listrik" => "tiang_listrik",
    "trafo listrik" => "trafo_listrik",
    "hidran" => "hidran",
    "rantai pasok" => "rantai_pasok",
    "reklame" => "reklame",
    "titik bench mark" => "titik_bm",
    "sungai" => "sungai"
];
if (isset($_GET['download']) && isset($_GET['subkelas'])) {
    $subkelas = strtolower(trim($_GET['subkelas']));
    if (!isset($mapSubkelasToTable[$subkelas])) {
        die("❌ Subkelas tidak ditemukan dalam mapping.");
    }
    $table = $mapSubkelasToTable[$subkelas];
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check->num_rows == 0) {
        die("❌ Tabel tidak ditemukan: $table");
    }
    $sql = "SELECT *, ST_AsGeoJSON(geometri) AS geojson FROM $table";
    $result = $conn->query($sql);
    if (!$result || $result->num_rows === 0) {
        die("❌ Tidak ada data untuk subkelas: $subkelas");
    }
    while (ob_get_level()) {
        ob_end_clean();
    }
    $tmpFile = __DIR__ . "/temp_" . uniqid() . ".geojson";
    $handle = fopen($tmpFile, 'w');
    fwrite($handle, '{"type":"FeatureCollection","features":[');
    $first = true;
    while ($row = $result->fetch_assoc()) {
        if (!$row["geojson"]) continue;
        $geometry = json_decode($row["geojson"], true);
        unset($row["geojson"], $row["geometri"]);
        $feature = [
            "type" => "Feature",
            "geometry" => $geometry,
            "properties" => $row
        ];
        if (!$first) fwrite($handle, ',');
        fwrite($handle, json_encode($feature, JSON_UNESCAPED_UNICODE));
        $first = false;
    }
    fwrite($handle, ']}');
    fclose($handle);
    header("Content-Type: application/geo+json");
    header("Content-Disposition: attachment; filename=\"$subkelas.geojson\"");
    header("Content-Transfer-Encoding: binary");
    header("Cache-Control: no-cache");
    header("Pragma: public");
    $fp = fopen($tmpFile, 'rb');
    while (!feof($fp)) {
        echo fread($fp, 8192);
        flush();
    }
    fclose($fp);
    unlink($tmpFile);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Download Data Utilitas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f9;
      color: #333;
      padding-top: 100px;
      padding-bottom: 20px;
    }

    h2,
    h4 {
      color: #003366;
      text-align: center;
      font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    }

    h4 {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .btn-primary {
      background-color: #003366 !important;
      border-color: #170080ff !important;
      font-weight: 600;
    }

    .btn-primary:hover {
      background-color: #040067ff !important;
      border-color: #0b0070ff !important;
    }

    @media (max-width: 575px) {
      h4 {
        font-size: 22px;
      }

      body {
        padding-left: 10px;
        padding-right: 10px;
      }
    }

    @media (min-width: 576px) and (max-width: 991px) {
      h4 {
        font-size: 25px;
      }
    }
  </style>
</head>

<body>
  <?php include "../../partials/sidebar.php"; ?>
  <?php include "../../partials/header.php"; ?>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-10 col-lg-8">
        <div class="card p-4">
          <h4>📦 Download Data Infrastruktur</h4>
          <form method="GET" id="formDownload">
            <div class="mb-3 card p-3">
              <h6 class="mb-3 fw-semibold">Pilih Kelas dan Subkelas</h6>
              <p class="text-muted mb-4" style="font-size: 0.9rem;">
            ℹ️Silakan pilih <strong>Kelas</strong> data yang ingin diunduh. Setelah memilih kelas, <strong>Subkelas</strong> akan tersedia.
          </p>
              <label class="form-label fw-semibold">Kelas</label>
              <select class="form-select" id="kelas" required>
                <option value="">-- Pilih Kelas --</option>
                <option value="atribut_jalan">🛤️ Atribut Jalan</option>
                <option value="halte">🚌 Halte</option>
                <option value="infrastruktur_pendukung">🏢 Infrastruktur Pendukung</option>
                <option value="jaringan_drainase">💧 Jaringan Drainase</option>
                <option value="jaringan_fiber_optik">📡 Jaringan Fiber Optik</option>
                <option value="jaringan_ipal">🏭 Jaringan Instalasi Pengolahan Air Limbah</option>
                <option value="jaringan_jalan">🛣️ Jaringan Jalan</option>
                <option value="jaringan_listrik">🔌 Jaringan Listrik</option>
                <option value="jaringan_pdam">🚰 Jaringan Perusahaan Daerah Air Minum</option>
                <option value="sungai">🏞️ Sungai</option>
              </select>

              <div class="mb-3 mt-3">
                <label class="form-label fw-semibold">Subkelas</label>
                <select class="form-select" name="subkelas" id="subkelas" required>
                  <option value="">-- Pilih Subkelas --</option>
                </select>
              </div>
              <button type="submit" name="download" value="1" class="btn btn-primary w-100">
              💾 Download GeoJSON
            </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    const subkelasMap = {
      atribut_jalan: [{
          val: 'Cermin Jalan',
          text: 'Cermin Jalan'
        },
        {
          val: 'Kamera Pengawas',
          text: 'Kamera Pengawas'
        },
        {
          val: 'Lampu Jalan',
          text: 'Lampu Jalan'
        },
        {
          val: 'Lampu Lalu Lintas',
          text: 'Lampu Lalu Lintas'
        },
        {
          val: 'Rambu Lalu Lintas',
          text: 'Rambu Lalu Lintas'
        }
      ],
      halte: [{
          val: 'Jalur Halte Trans',
          text: 'Jalur Halte Trans'
        },
        {
          val: 'Titik Halte',
          text: 'Titik Halte'
        }
      ],
      jaringan_drainase: [{
          val: 'Inlet',
          text: 'Inlet'
        },
        {
          val: 'Manhole Drainase',
          text: 'Manhole Drainase'
        },
        {
          val: 'Sumur Resapan',
          text: 'Sumur Resapan'
        },
        {
          val: 'Zona Drainase',
          text: 'Zona Drainase'
        }
      ],
      jaringan_fiber_optik: [{
          val: 'Kabel Fiber Optik',
          text: 'Kabel Fiber Optik'
        },
        {
          val: 'Manhole Fiber Optik',
          text: 'Manhole Fiber Optik'
        },
        {
          val: 'Tiang Fiber Optik',
          text: 'Tiang Fiber Optik'
        }
      ],
      jaringan_ipal: [{
          val: 'Manhole Saluran Air Limbah',
          text: 'Manhole Saluran Air Limbah'
        },
        {
          val: 'Pipa Glontor',
          text: 'Pipa Glontor'
        },
        {
          val: 'Pipa Induk',
          text: 'Pipa Induk'
        },
        {
          val: 'Pipa Lateral',
          text: 'Pipa Lateral'
        }
      ],
      jaringan_jalan: [{
          val: 'Jalan Kota',
          text: 'Jalan Kota'
        },
        {
          val: 'Jalan Lingkungan',
          text: 'Jalan Lingkungan'
        },
        {
          val: 'Jalur Pemandu',
          text: 'Jalur Pemandu'
        },
        {
          val: 'Jembatan',
          text: 'Jembatan'
        },
        {
          val: 'Trotoar',
          text: 'Trotoar'
        }
      ],
      jaringan_listrik: [{
          val: 'Jaringan Kabel Listrik Tegangan Menengah',
          text: 'Jaringan Kabel Listrik Tegangan Menengah'
        },
        {
          val: 'Jaringan Kabel Listrik Tegangan Rendah',
          text: 'Jaringan Kabel Listrik Tegangan Rendah'
        },
        {
          val: 'Rumah Kabel',
          text: 'Rumah Kabel'
        },
        {
          val: 'Tiang Listrik',
          text: 'Tiang Listrik'
        },
        {
          val: 'Trafo Listrik',
          text: 'Trafo Listrik'
        }
      ],
      jaringan_pdam: [{
        val: 'Jaringan PDAM',
        text: 'Jaringan Perusahaan Daerah Air Minum'
      }],
      infrastruktur_pendukung: [{
          val: 'Hidran',
          text: 'Hidran'
        },
        {
          val: 'Rantai Pasok',
          text: 'Rantai Pasok'
        },
        {
          val: 'Reklame',
          text: 'Reklame'
        },
        {
          val: 'Titik Bench Mark',
          text: 'Titik Bench Mark'
        }
      ],
      sungai: [{
        val: 'Sungai',
        text: 'Sungai'
      }]
    };

    document.getElementById('kelas').addEventListener('change', function() {
      const kelas = this.value;
      const subkelasSelect = document.getElementById('subkelas');
      subkelasSelect.innerHTML = '<option value="">-- Pilih Subkelas --</option>';

      if (kelas && subkelasMap[kelas]) {
        subkelasMap[kelas].forEach(opt => {
          const option = document.createElement('option');
          option.value = opt.val;
          option.textContent = opt.text;
          subkelasSelect.appendChild(option);
        });
      }
    });
  </script>
</body>
</html>
