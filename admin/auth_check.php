<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'CallStaff') {
    echo "<h2 style='color:red; text-align:center; margin-top:50px;'>⛔ Truy cập bị từ chối! Bạn không phải Admin.</h2>";
    echo "<p style='text-align:center;'><a href='../index.php'>Quay về trang chủ</a></p>";
    exit();
}
?>