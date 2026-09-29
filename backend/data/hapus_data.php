<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

require_once __DIR__ . '/../db/koneksi.php';
require_once __DIR__ . '/../log/log_akses.php';

if (!isset($_SESSION['user'])) {
    die("<script>alert('Sesi pengguna berakhir. Silakan login kembali.'); history.back();</script>");
}

/*
 * Koneksi database sekarang menggunakan PDO PostgreSQL.
 * Pastikan koneksi.php menghasilkan:
 * $conn = new PDO(...);
 */
if (!($conn instanceof PDO)) {
    die("<script>alert('Koneksi database tidak valid.'); history.back();</script>");
}

$userSession = $_SESSION['user'];

$user = [
    'username' => $userSession['username'] ?? 'guest',
    'unit'     => $userSession['unit'] ?? ($userSession['role'] ?? 'guest'),
    'role'     => $userSession['role'] ?? 'guest'
];

/*
 * Ambil semua input terlebih dahulu.
 */
$subkelas = trim((string)($_POST['subkelas'] ?? ''));
$id       = trim((string)($_POST['id'] ?? ''));
$idField  = trim((string)($_POST['id_field'] ?? ''));
$nama     = trim((string)($_POST['nama_penghapus'] ?? ''));
$unit     = trim((string)($_POST['unit_penghapus'] ?? ''));
$alasan   = trim((string)($_POST['alasan_penghapusan'] ?? ''));
$kelas    = trim((string)($_POST['kelas'] ?? ''));

$captcha_input   = strtoupper(trim((string)($_POST['captcha_input'] ?? '')));
$captcha_session = strtoupper(trim((string)($_SESSION['captcha_code'] ?? '')));

/*
 * Validasi input dasar.
 */
if ($subkelas === '' || $id === '' || $idField === '') {
    die("<script>alert('Data tidak lengkap.'); history.back();</script>");
}

if ($nama === '' || $unit === '' || $alasan === '') {
    die("<script>alert('Nama, unit, dan alasan wajib diisi.'); history.back();</script>");
}

/*
 * Validasi CAPTCHA.
 *
 * CAPTCHA dibuat oleh update_data.php:
 * $_SESSION['captcha_code'] = $captcha_code;
 *
 * Perbandingan dibuat case-insensitive karena keduanya diubah ke uppercase.
 */
if (
    $captcha_session === '' ||
    $captcha_input === '' ||
    !hash_equals($captcha_session, $captcha_input)
) {
    die("<script>alert('Kode verifikasi salah!'); history.back();</script>");
}

/*
 * Daftar tabel yang memang diperbolehkan dihapus.
 * Nama tabel tidak boleh langsung berasal dari input user tanpa whitelist.
 */
$allowedTables = [
    'bangunan',
    'cermin_jalan',
    'hidran',
    'inlet',
    'jalan_kota',
    'jalan_lingkungan',
    'jalur_pemandu',
    'jalur_halte',
    'jembatan',
    'jaringan_pdam',
    'kabel_fo',
    'kamera_pengawas',
    'lampu_jalan',
    'lampu_lalin',
    'manhole_drainase',
    'manhole_fo',
    'manhole_ipal',
    'manhole_sal',
    'pipa_glontor',
    'pipa_induk',
    'pipa_lateral',
    'rambu_lalin',
    'rantai_pasok',
    'reklame',
    'rumah_kabel',
    'sumur_resapan',
    'sungai',
    'tegangan_menengah',
    'tegangan_rendah',
    'tiang_fo',
    'tiang_listrik',
    'titik_bm',
    'titik_halte',
    'trafo_listrik',
    'trotoar',
    'zona_drainase'
];

if (!in_array($subkelas, $allowedTables, true)) {
    die("<script>alert('Subkelas tidak valid.'); history.back();</script>");
}

/*
 * Primary key yang diperbolehkan untuk masing-masing tabel.
 */
$primaryKeys = [
    'bangunan'          => 'id',
    'cermin_jalan'      => 'id',
    'kamera_pengawas'   => 'id',
    'lampu_jalan'       => 'id',
    'lampu_lalin'       => 'id',
    'rantai_pasok'      => 'id',
    'rambu_lalin'       => 'id',
    'reklame'           => 'id_reklame',
    'titik_bm'          => 'id',
    'hidran'            => 'id',
    'sumur_resapan'     => 'id',
    'inlet'             => 'id',
    'zona_drainase'     => 'id',
    'manhole_drainase'  => 'id',
    'manhole_fo'        => 'id',
    'manhole_ipal'      => 'id',
    'manhole_sal'       => 'id',
    'tiang_fo'          => 'id',
    'kabel_fo'          => 'id',
    'pipa_glontor'      => 'id',
    'pipa_induk'        => 'id',
    'pipa_lateral'      => 'id',
    'jalan_kota'        => 'id_jalan',
    'jalan_lingkungan'  => 'id_jalan',
    'jalur_pemandu'     => 'id',
    'jembatan'          => 'id',
    'trotoar'           => 'id_trotoar',
    'rumah_kabel'       => 'id',
    'tegangan_menengah' => 'id_tm',
    'tegangan_rendah'   => 'id_tr',
    'tiang_listrik'     => 'id',
    'trafo_listrik'     => 'id',
    'jaringan_pdam'     => 'id_pdam',
    'titik_halte'       => 'id',
    'jalur_halte'       => 'id',
    'sungai'            => 'id'
];

/*
 * id_field yang dikirim form harus sesuai dengan primary key
 * tabel tersebut.
 *
 * Ini juga mencegah nama kolom dari POST digunakan sembarangan
 * sebagai identifier SQL.
 */
$expectedIdField = $primaryKeys[$subkelas] ?? null;

if ($expectedIdField === null) {
    die("<script>alert('Primary key untuk subkelas ini belum dikonfigurasi.'); history.back();</script>");
}

if ($idField !== $expectedIdField) {
    die("<script>alert('Kolom ID tidak sesuai dengan struktur tabel.'); history.back();</script>");
}

/*
 * Pastikan tabel dan kolom benar-benar ada di PostgreSQL.
 * Tidak menggunakan SHOW COLUMNS karena itu sintaks MySQL.
 */
try {
    $checkColumn = $conn->prepare("
        SELECT column_name
        FROM information_schema.columns
        WHERE table_schema = current_schema()
          AND table_name = :table_name
          AND column_name = :column_name
        LIMIT 1
    ");

    $checkColumn->execute([
        ':table_name'  => $subkelas,
        ':column_name' => $idField
    ]);

    if (!$checkColumn->fetch(PDO::FETCH_ASSOC)) {
        die("<script>alert('Kolom ID tidak ditemukan di tabel $subkelas.'); history.back();</script>");
    }

    /*
     * Cek terlebih dahulu apakah data dengan ID tersebut memang ada.
     */
    $tableSql = '"' . str_replace('"', '""', $subkelas) . '"';
    $fieldSql = '"' . str_replace('"', '""', $idField) . '"';

    $checkData = $conn->prepare(
        "SELECT $fieldSql FROM $tableSql WHERE $fieldSql = :id LIMIT 1"
    );

    $checkData->execute([
        ':id' => $id
    ]);

    if (!$checkData->fetch(PDO::FETCH_ASSOC)) {
        die("<script>alert('Data yang akan dihapus tidak ditemukan.'); history.back();</script>");
    }

    /*
     * Mulai transaksi agar DELETE dan log penghapusan konsisten.
     */
    $conn->beginTransaction();

    /*
     * DELETE menggunakan PDO PostgreSQL.
     */
    $deleteStmt = $conn->prepare(
        "DELETE FROM $tableSql WHERE $fieldSql = :id"
    );

    $deleteStmt->execute([
        ':id' => $id
    ]);

    if ($deleteStmt->rowCount() < 1) {
        throw new RuntimeException('Tidak ada data yang berhasil dihapus.');
    }

    /*
     * Simpan log penghapusan.
     */
    $logStmt = $conn->prepare("
        INSERT INTO log_penghapusan
        (
            subkelas,
            id_data,
            nama_penghapus,
            unit_penghapus,
            alasan_penghapusan
        )
        VALUES
        (
            :subkelas,
            :id_data,
            :nama_penghapus,
            :unit_penghapus,
            :alasan_penghapusan
        )
    ");

    $logStmt->execute([
        ':subkelas'          => $subkelas,
        ':id_data'           => $id,
        ':nama_penghapus'    => $nama,
        ':unit_penghapus'    => $unit,
        ':alasan_penghapusan'=> $alasan
    ]);

    /*
     * Commit jika DELETE dan log berhasil.
     */
    $conn->commit();

    /*
     * Catat aktivitas ke log_akses.
     *
     * Format ini mengikuti pemanggilan logAkses yang digunakan
     * pada update_data.php / download_data.php di project ini:
     * logAkses($conn, $user, $menu, $keterangan)
     */
    try {
        $keteranganAkses =
            "Menghapus data ID $id dari $subkelas. " .
            "Nama: $nama. Unit: $unit. Alasan: $alasan.";

        logAkses(
            $conn,
            $userSession,
            "hapus_data.php",
            $keteranganAkses
        );
    } catch (Throwable $logError) {
        /*
         * Jangan membatalkan DELETE hanya karena log_akses gagal.
         * Data utama dan log_penghapusan sudah berhasil disimpan.
         */
    }

    /*
     * Kembali ke halaman update_data.
     */
    $redirect = 'update_data.php?kelas=' . urlencode($kelas)
              . '&subkelas=' . urlencode($subkelas)
              . '&refresh=1';

    $namaJs   = json_encode($nama, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $unitJs   = json_encode($unit, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $alasanJs = json_encode($alasan, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $urlJs    = json_encode($redirect, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

    echo "<script>
        alert(
            '✅ Data berhasil dihapus.\\n' +
            'Nama: ' + $namaJs + '\\n' +
            'Unit: ' + $unitJs + '\\n' +
            'Alasan: ' + $alasanJs
        );
        window.location.href = $urlJs;
    </script>";

} catch (Throwable $e) {

    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    /*
     * Jangan tampilkan detail koneksi/database ke user.
     * Detail tetap bisa dilihat di server log.
     */
    error_log(
        'hapus_data.php error: ' .
        $e->getMessage()
    );

    $message = $e->getMessage();

    /*
     * Pesan user-friendly untuk error PostgreSQL umum.
     */
    if (stripos($message, 'foreign key') !== false) {
        $message = 'Data tidak dapat dihapus karena masih digunakan oleh data lain.';
    } elseif (stripos($message, 'permission denied') !== false) {
        $message = 'Database tidak memberikan izin untuk menghapus data.';
    } else {
        $message = 'Terjadi kesalahan saat menghapus data.';
    }

    $messageJs = json_encode(
        $message,
        JSON_UNESCAPED_UNICODE |
        JSON_HEX_TAG |
        JSON_HEX_APOS |
        JSON_HEX_QUOT |
        JSON_HEX_AMP
    );

    echo "<script>
        alert('❌ ' + $messageJs);
        history.back();
    </script>";
}
?>
