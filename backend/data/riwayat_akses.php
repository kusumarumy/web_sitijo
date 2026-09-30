<?php
session_start();

include __DIR__ . '/../../backend/db/koneksi.php';
include __DIR__ . '/../../backend/auth/cek_role.php';
include __DIR__ . '/../log/log_akses.php';

$unit = $_SESSION['user']['unit'] ?? '';

if ($unit !== 'admin') {
    logAkses(
        $conn,
        $_SESSION['user'],
        "riwayat_akses.php",
        "Akses Ditolak",
        "User mencoba membuka menu admin"
    );

    echo "<script>
        alert('Akses ditolak!');
        window.location.href='/index.php';
    </script>";
    exit();
}

logAkses(
    $conn,
    $_SESSION['user'],
    "riwayat_akses.php",
    "Melihat riwayat akses pengguna."
);

include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/sidebar.php';

$sql = "SELECT * FROM log_akses ORDER BY waktu_akses DESC";

$stmt = $conn->query($sql);

$logs = $stmt
    ? $stmt->fetchAll(PDO::FETCH_ASSOC)
    : [];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Akses</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"
    >

    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    >

    <style>
        body {
            margin-top: 100px;
            margin-left: 80px;
            margin-right: 50px;
        }

        .filter-wrapper {
            margin-bottom: 20px;
        }

        .filter-label {
            font-weight: 600;
            margin-bottom: 6px;
        }

        #logTable {
            width: 100% !important;
        }

        #logTable td {
            vertical-align: top;
        }

        /* Keterangan */
        #logTable td:nth-child(7) {
            min-width: 350px;
            max-width: 500px;
            white-space: normal;
            word-break: break-word;
        }

        /* User Agent */
        #logTable td:nth-child(9) {
            min-width: 250px;
            max-width: 350px;
            white-space: normal;
            word-break: break-word;
        }

        /* Tombol DataTables */
        .dt-buttons {
            margin-bottom: 15px;
        }

        .dt-buttons .btn {
            margin-right: 3px;
        }
    </style>
</head>

<body>

    <!-- =========================================
         FILTER TANGGAL
    ========================================== -->

    <div class="filter-wrapper">

        <div class="row align-items-end g-3">

            <div class="col-md-3">

                <label
                    for="startDate"
                    class="form-label filter-label"
                >
                    Dari Tanggal
                </label>

                <input
                    type="date"
                    id="startDate"
                    class="form-control"
                >

            </div>

            <div class="col-md-3">

                <label
                    for="endDate"
                    class="form-label filter-label"
                >
                    Sampai Tanggal
                </label>

                <input
                    type="date"
                    id="endDate"
                    class="form-control"
                >

            </div>

            <div class="col-md-3">

                <button
                    id="filterBtn"
                    class="btn btn-primary"
                >
                    <i class="bi bi-funnel"></i>
                    Filter
                </button>

                <button
                    id="clearFilterBtn"
                    class="btn btn-secondary"
                >
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Reset
                </button>

            </div>

        </div>

    </div>


    <!-- =========================================
         TABLE
    ========================================== -->

    <div class="table-responsive">

        <table
            id="logTable"
            class="table table-striped table-bordered"
        >

            <thead class="table-primary">

                <tr>
                    <th>#</th>
                    <th>Waktu Akses</th>
                    <th>Username</th>
                    <th>Unit</th>
                    <th>Menu</th>
                    <th>Aksi</th>
                    <th>Keterangan</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach ($logs as $i => $log): ?>

                    <tr>

                        <td>
                            <?= $i + 1 ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $log['waktu_akses'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $log['username'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $log['unit'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $log['menu'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $log['aksi'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $log['keterangan'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $log['ip_address'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $log['user_agent'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>


    <!-- =========================================
         JAVASCRIPT
    ========================================== -->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>


    <script>

        $(document).ready(function () {

            var table = $('#logTable').DataTable({

                pageLength: 25,

                order: [
                    [1, "desc"]
                ],

                dom: 'Bfrtip',

                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5',
                    'print'
                ]

            });


            /*
             * =========================================
             * FILTER TANGGAL
             * =========================================
             */

            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {

                    if (settings.nTable.id !== 'logTable') {
                        return true;
                    }

                    var start = $('#startDate').val();
                    var end = $('#endDate').val();

                    var dateValue = data[1] || '';

                    /*
                     * PostgreSQL:
                     *
                     * 2026-09-30 03:53:42.801282
                     *
                     * Ambil:
                     *
                     * 2026-09-30
                     */

                    var date = dateValue.substring(0, 10);

                    if (
                        (start === '' || date >= start) &&
                        (end === '' || date <= end)
                    ) {
                        return true;
                    }

                    return false;
                }
            );


            /*
             * =========================================
             * BUTTON FILTER
             * =========================================
             */

            $('#filterBtn').on('click', function () {

                table.draw();

            });


            /*
             * =========================================
             * BUTTON RESET
             * =========================================
             */

            $('#clearFilterBtn').on('click', function () {

                $('#startDate').val('');

                $('#endDate').val('');

                table.search('');

                table.columns().search('');

                table.draw();

            });


            /*
             * =========================================
             * FILTER SAAT TEKAN ENTER
             * =========================================
             */

            $('#startDate, #endDate').on('change', function () {

                table.draw();

            });

        });

    </script>

</body>
</html>
