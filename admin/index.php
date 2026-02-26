<?php 
include 'auth_check.php'; // Chặn User thường ngay lập tức
include '../config/db.php'; 
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: #eef2f5; } /* Màu nền riêng cho Admin */
    </style>
</head>
<body>

<header style="background-color: var(--secondary);">
    <nav>
        <a href="#" class="logo">⚙️ Admin Panel</a>
        <div class="nav-links">
            <a href="index.php">Tổng Quan</a>
            <a href="manage_bookings.php">Quản Lý Đơn</a>
            <a href="../logout.php" style="background: #d9534f; padding: 5px 10px; border-radius: 4px;">Đăng Xuất</a>
        </div>
    </nav>
</header>

<div class="container">
    <h1>Xin chào, <?php echo $_SESSION['name']; ?></h1>
    <p>Vai trò của bạn: <strong><?php echo $_SESSION['role']; ?></strong></p>
    
    <div style="display: flex; gap: 20px; margin-top: 30px;">
        <div style="flex: 1; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center;">
            <h3 style="color: var(--primary);">Đơn Hàng Mới</h3>
            <?php
            $res = $conn->query("SELECT COUNT(*) as c FROM Bookings WHERE Status='Pending'");
            $row = $res->fetch_assoc();
            echo "<h1 style='font-size: 3rem; margin: 10px 0;'>" . $row['c'] . "</h1>";
            ?>
        </div>

        <div style="flex: 1; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center;">
            <h3 style="color: var(--accent);">Xe Đang Rảnh</h3>
            <?php
            $res = $conn->query("SELECT COUNT(*) as c FROM Vehicles WHERE IsAvailable=1");
            $row = $res->fetch_assoc();
            echo "<h1 style='font-size: 3rem; margin: 10px 0;'>" . $row['c'] . "</h1>";
            ?>
        </div>
    </div>
</div>

</body>
</html>