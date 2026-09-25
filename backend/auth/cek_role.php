<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: /backend/auth/signin.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
$canAdd = true;
$canEdit = true;
$canDelete = true;
?>
