<?php
session_start();
include __DIR__ . '/../../backend/db/koneksi.php';
include __DIR__ . '/../../backend/auth/cek_role.php';
include __DIR__ . '/../log/log_akses.php';

$unit = $_SESSION['user']['unit'] ?? '';
logAkses($conn, $_SESSION['user'], "riwayat_akses.php", "Melihat riwayat akses pengguna.");

if ($unit !== 'admin') {
    logAkses($conn, $_SESSION['user'], "riwayat_akses.php", "Akses Ditolak", "User mencoba membuka menu admin");
    echo "<script>alert('Akses ditolak!'); window.location.href='/index.php';</script>";
    exit();
}

include __DIR__ . '/../../partials/header.php';
include __DIR__ . '/../../partials/sidebar.php';

$sql = "SELECT * FROM log_akses ORDER BY waktu_akses DESC";
$result = $conn->query($sql);
$logs = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Akses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body style="margin-top : 100px; margin-left : 80px; margin-right: 50px">
    <div class="row mb-3">
        <div class="col-md-3">
            <input type="date" id="startDate" class="form-control" placeholder="Dari">
        </div>
        <div class="col-md-3">
            <input type="date" id="endDate" class="form-control" placeholder="Sampai">
        </div>
        <div class="col-md-3">
            <button id="filterBtn" class="btn btn-primary">Filter</button>
            <button id="clearFilterBtn" class="btn btn-secondary">Reset</button>
        </div>
    </div>
    <div class="table-responsive">
        <table id="logTable" class="table table-striped table-bordered">
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
                    <td><?= $i + 1 ?></td>
                    <td><?= $log['waktu_akses'] ?></td>
                    <td><?= htmlspecialchars($log['username'] ?? '') ?></td>
                    <td><?= htmlspecialchars($log['unit'] ?? '') ?></td>
                    <td><?= htmlspecialchars($log['menu'] ?? '') ?></td>
                    <td><?= htmlspecialchars($log['aksi'] ?? '') ?></td>
                    <td><?= htmlspecialchars($log['keterangan'] ?? '') ?></td>
                    <td><?= htmlspecialchars($log['ip_address'] ?? '') ?></td>
                    <td><?= htmlspecialchars($log['user_agent'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

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
        $(document).ready(function() {
            var table = $('#logTable').DataTable({
                "pageLength": 25,
                "order": [[1, "desc"]],
                dom: 'Bfrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5',
                    'print'
                ]
            });
            $('#filterBtn').on('click', function() {
                var start = $('#startDate').val();
                var end = $('#endDate').val();
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        var date = data[1]; 
                        if ((start === "" || date >= start) && (end === "" || date <= end)) {
                            return true;
                        }
                        return false;
                    }
                );
                table.draw();
                $.fn.dataTable.ext.search.pop();
            });
            $('#clearFilterBtn').on('click', function() {
                $('#startDate').val('');
                $('#endDate').val('');
                table.search('').columns().search('').draw();
            });
        });
    </script>
</body>
</html>