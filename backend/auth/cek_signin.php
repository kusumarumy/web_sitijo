<?php

session_start();

require_once __DIR__ . "/../db/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: signin.php");
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$redirect = !empty($_POST['redirect'])
    ? $_POST['redirect']
    : '/index.php';


try {

    /*
     * Cari user berdasarkan username
     */
    $stmt = $conn->prepare("
        SELECT *
        FROM users
        WHERE username = ?
        LIMIT 1
    ");

    $stmt->execute([$username]);

    $user = $stmt->fetch();


    /*
     * Username ditemukan
     */
    if ($user) {

        /*
         * Verifikasi password
         */
        if (password_verify($password, $user['password'])) {

            $_SESSION['user'] = [
                'username' => $user['username'],
                'unit' => $user['unit'] ?? 'guest'
            ];


            /*
             * Remember me
             */
            if (isset($_POST['remember'])) {

                setcookie(
                    'remember_user',
                    $user['username'],
                    time() + (86400 * 7),
                    "/"
                );
            }


            /*
             * Login berhasil
             */
            header("Location: " . $redirect);
            exit();

        } else {

            $_SESSION['error'] = "Password salah!";
        }

    } else {

        $_SESSION['error'] = "Username tidak ditemukan!";
    }

} catch (PDOException $e) {

    error_log("Login database error: " . $e->getMessage());

    $_SESSION['error'] = "Terjadi kesalahan pada database.";
}


/*
 * Login gagal
 */
$redirectParam = urlencode($redirect);

header(
    "Location: signin.php?redirect=" . $redirectParam
);

exit();
