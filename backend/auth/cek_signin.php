<?php
session_start();
include "../../backend/db/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: ../../backend/auth/signin.php");
  exit();
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$redirect = !empty($_POST['redirect']) ? $_POST['redirect'] : '/index.php';

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
  $user = $result->fetch_assoc();
  if (password_verify($password, $user['password'])) {
    $_SESSION['user'] = [
      'username' => $user['username'],
      'unit' => $user['unit'] ?? 'guest'
    ];

    if (isset($_POST['remember'])) {
      setcookie('remember_user', $user['username'], time() + (86400 * 7), "/");
    }
    header("Location: $redirect");
    exit();
  } else {
    $_SESSION['error'] = "Password salah!";
  }
} else {
  $_SESSION['error'] = "Username tidak ditemukan!";
}
header("Location: " . dirname($_SERVER['PHP_SELF']) . "/signin.php?redirect=$redirect");
exit();
?>