<?php
include '../../backend/db/koneksi.php';
include '../../backend/auth/cek_role.php';
include __DIR__ . '/../log/log_akses.php';
logAkses($conn, $_SESSION['user'], "update_proses.php", "Mengubah Atribut Data");

$subkelas = $_POST['subkelas'] ?? $_GET['subkelas'] ?? '';
$data_id = $_GET['id'] ?? $_POST['id'] ?? '';
$subkelas = trim($subkelas);
$data_id = trim($data_id);
$id = $data_id;

$subkelas_map = [
    "Rambu Lalu Lintas" => "rambu_lalin",
    "Rantai Pasok" => "rantai_pasok",
    "Cermin Jalan" => "cermin_jalan",
    "Kamera Pengawas" => "kamera_pengawas",
    "Lampu Jalan" => "lampu_jalan",
    "Lampu Lalu Lintas" => "lampu_lalin",
    "Titik Halte" => "titik_halte",
    "Jalur Halte Trans" => "jalur_halte",
    "Inlet" => "inlet",
    "Manhole Drainase" => "manhole_drainase",
    "Sumur Resapan" => "sumur_resapan",
    "Zona Drainase" => "zona_drainase",
    "Kabel Fiber Optik" => "kabel_fo",
    "Manhole Fiber Optik" => "manhole_fo",
    "Tiang Fiber Optik" => "tiang_fo",
    "Manhole Instalasi Pengolahan Air Limbah" => "manhole_ipal",
    "Pipa Glontor" => "pipa_glontor",
    "Pipa Induk" => "pipa_induk",
    "Pipa Lateral" => "pipa_lateral",
    "Hidran" => "hidran",
    "Jalan Kota" => "jalan_kota",
    "Jalan Lingkungan" => "jalan_lingkungan",
    "Jalur Pemandu" => "jalur_pemandu",
    "Jembatan" => "jembatan",
    "Trotoar" => "trotoar",
    "Jaringan Kabel Listrik Tegangan Menengah" => "tegangan_menengah",
    "Jaringan Kabel Listrik Tegangan Rendah" => "tegangan_rendah",
    "Rumah Kabel" => "rumah_kabel",
    "Tiang Listrik" => "tiang_listrik",
    "Trafo Listrik" => "trafo_listrik",
    "Jaringan PDAM" => "jaringan_pdam",
    "Sungai" => "sungai",
    "Reklame" => "reklame",
    "Titik Bench Mark" => "titik_bm",
];
if (array_key_exists($subkelas, $subkelas_map)) {
    $subkelas = $subkelas_map[$subkelas];
} else {
    $subkelas = strtolower(str_replace(' ', '_', $subkelas));
}

if (!isset($user) || !is_array($user)) {
    $user = $_SESSION['user'] ?? ['username' => 'unknown', 'unit' => 'guest'];
}

$keterangan = "Mengubah data ID $data_id di $subkelas";
logAkses($conn, $user['username'], $user['unit'], $subkelas, "Update", $keterangan);
$subkelas_list = [
    "bangunan",
    "cermin_jalan",
    "hidran",
    "inlet",
    "jalan_kota",
    "jalan_lingkungan",
    "jalur_pemandu",
    "jalur_halte",
    "jembatan",
    "jaringan_pdam",
    "kabel_fo",
    "kamera_pengawas",
    "lampu_jalan",
    "lampu_lalin",
    "manhole_drainase",
    "manhole_fo",
    "manhole_ipal",
    "pipa_glontor",
    "pipa_induk",
    "pipa_lateral",
    "rambu_lalin",
    "rantai_pasok",
    "reklame",
    "rumah_kabel",
    "sumur_resapan",
    "sungai",
    "tegangan_menengah",
    "tegangan_rendah",
    "tiang_fo",
    "tiang_listrik",
    "titik_bm",
    "titik_halte",
    "trafo_listrik",
    "trotoar",
    "zona_drainase"
];
$primary_keys = [
    "cermin_jalan" => "id",
    "kamera_pengawas" => "id",
    "lampu_jalan" => "id",
    "lampu_lalin" => "id",
    "rantai_pasok" => "id",
    "rambu_lalin" => "id",
    "reklame" => "id_reklame",
    "titik_bm" => "id",
    "hidran" => "id",
    "sumur_resapan" => "id",
    "inlet" => "id",
    "zona_drainase" => "id",
    "manhole_drainase" => "id",
    "manhole_fo" => "id",
    "tiang_fo" => "id",
    "kabel_fo" => "id",
    "manhole_ipal" => "id",
    "pipa_glontor" => "id",
    "pipa_induk" => "id",
    "pipa_lateral" => "id",
    "jalan_kota" => "id_jalan",
    "jalan_lingkungan" => "id_jalan",
    "jalur_pemandu" => "id",
    "jembatan" => "id",
    "trotoar" => "id_trotoar",
    "rumah_kabel" => "id",
    "tegangan_menengah" => "id_tm",
    "tegangan_rendah" => "id_tr",
    "tiang_listrik" => "id",
    "trafo_listrik" => "id",
    "jaringan_pdam" => "id_pdam",
    "titik_halte" => "id",
    "jalur_halte" => "id",
    "sungai" => "id"
];
if (!in_array($subkelas, $subkelas_list, true)) {
    die("<div style='text-align:center;margin-top:50px;color:red;'>Subkelas tidak valid ($subkelas).</div>");
}

$pesan = '';
$data_edit = [];
$fields = [];
$res = mysqli_query($conn, "SELECT * FROM `$subkelas` LIMIT 1");
if ($res) {
    $fields = array_keys(mysqli_fetch_assoc($res));
} else {
    die("Gagal mengambil struktur tabel: " . mysqli_error($conn));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $pk = $_POST['pk'];
    $id_post = $_POST['id'];
    $set = [];
    foreach ($_POST as $field => $val) {
        if (in_array($field, ['update', 'id', 'pk', 'subkelas'])) continue;
        if ($field === 'geometri') continue;
        $val = mysqli_real_escape_string($conn, $val);
        $set[] = "`$field` = '$val'";
    }
    $setStr = implode(',', $set);
    $query = "UPDATE `$subkelas` SET $setStr WHERE `$pk` = '$id_post'";
    if (mysqli_query($conn, $query)) {
        $pesan = "<div class='alert alert-success mt-3'>✅ Data berhasil diperbarui.</div>";
    } else {
        $pesan = "<div class='alert alert-danger mt-3'>❌ Gagal memperbarui data: " . mysqli_error($conn) . "</div>";
    }
    $id = $id_post;
}
if ($id) {
    $pk = $primary_keys[$subkelas] ?? $fields[0];
$q = mysqli_query($conn, "SELECT * FROM `$subkelas` WHERE `$pk` = '$id'");
    $data_edit = mysqli_fetch_assoc($q);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Data <?= ucfirst(str_replace('_', ' ', $subkelas)) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
        }

        header {
            background-color: #004aad;
            color: white;
            padding: 15px 30px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 100px;
        }

        label {
            font-weight: 500;
        }

        input[readonly] {
            background-color: #e9ecef;
        }

        .btn-primary {
            background-color: #004aad;
            border-color: #004aad;
        }

        .btn-primary:hover {
            background-color: #003b8e;
        }

        .back-link {
            text-decoration: none;
            color: #004aad;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        footer {
            margin-top: 50px;
            padding: 15px;
            font-size: 14px;
            text-align: center;
            color: #777;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <?php include "../../partials/sidebar.php"; ?>
    <?php include "../../partials/header.php"; ?>

    <div class="container my-5">
        <div class="card p-4">
            <h5 class="fw-semibold mb-3">Formulir Edit Data</h5>
            <?= $pesan ?>
            <?php if ($data_edit): ?>
                <form method="POST" action="">
                    <input type="hidden" name="subkelas" value="<?= htmlspecialchars($subkelas) ?>">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($data_edit[$fields[0]]) ?>">
                    <input type="hidden" name="pk" value="<?= $pk ?>">
                    <div class="row">
                        <?php foreach ($data_edit as $field => $value): ?>
                            <?php if ($field === 'geometri') continue; ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?= ucfirst(str_replace('_', ' ', $field)) ?></label>
                                <?php if (strtolower($field) === strtolower($fields[0])): ?>
                                    <input type="text" name="<?= htmlspecialchars($field) ?>"
                                        value="<?= htmlspecialchars($value) ?>"
                                        class="form-control" readonly>
                                <?php else: ?>
                                    <input type="text" name="<?= htmlspecialchars($field) ?>"
                                        value="<?= htmlspecialchars($value) ?>"
                                        class="form-control">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" name="update" class="btn btn-primary px-4">
                            💾 Simpan Perubahan
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-warning mt-3">⚠️ Data tidak ditemukan.</div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        © <?= date('Y') ?> Dinas Pekerjaan Umum, Perumahan, dan Kawasan Permukiman Kota Yogyakarta
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>