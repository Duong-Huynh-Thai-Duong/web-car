<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$booking_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT b.*, d.FullName as DriverName, d.PhoneNumber as DriverPhone, 
        v.RegNumber, v.MakeModel, v.Color 
        FROM Bookings b 
        LEFT JOIN Drivers d ON b.DriverID = d.DriverID 
        LEFT JOIN Vehicles v ON b.VehicleID = v.VehicleID 
        WHERE b.BookingID = '$booking_id' AND b.CustomerID = '$user_id'";
        
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$booking = $result->fetch_assoc();
$status = $booking['Status'];
?>
<?php include 'includes/header.php'; ?>

<style>
.tracking-container { max-width: 600px; margin: 40px auto; padding: 20px; background: var(--white); border-radius: 16px; box-shadow: var(--shadow); }
.status-timeline { display: flex; justify-content: space-between; position: relative; margin-bottom: 40px; margin-top: 20px; }
.status-timeline::before { content: ''; position: absolute; top: 15px; left: 0; width: 100%; height: 4px; background: #e0e0e0; z-index: 1; }
.step { position: relative; z-index: 2; text-align: center; flex: 1; }
.step-icon { width: 34px; height: 34px; border-radius: 50%; background: #e0e0e0; color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; border: 4px solid var(--white); }
.step.active .step-icon { background: var(--primary); }
.step.done .step-icon { background: var(--primary); }
.step-text { font-size: 0.85rem; font-weight: bold; color: var(--secondary); }
.grab-driver-card { background: var(--light); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 20px; border-left: 5px solid var(--primary); margin-top: 20px; }
.driver-avatar { width: 60px; height: 60px; background: var(--white); border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 1.8rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.driver-info { flex: 1; }
.driver-name { font-size: 1.2rem; font-weight: 800; color: var(--secondary); margin-bottom: 5px; }
.vehicle-plate { background: var(--white); padding: 5px 10px; border-radius: 6px; font-family: monospace; font-weight: bold; font-size: 1.1rem; display: inline-block; border: 1px solid #ccc; }
.vehicle-model { color: #666; font-size: 0.9rem; margin-top: 5px; font-weight: 600; }
.btn-call { background: var(--primary); color: var(--white); padding: 10px 20px; border-radius: 50px; text-decoration: none; font-weight: bold; display: inline-block; transition: 0.3s; }
.btn-call:hover { background: #3da885; }
.trip-details { margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
.detail-row { display: flex; justify-content: space-between; margin-bottom: 15px; }
.detail-label { color: #666; font-weight: bold; font-size: 0.9rem; }
.detail-value { color: var(--secondary); font-weight: 800; text-align: right; }
</style>

<div class="tracking-container">
    <h2 style="text-align: center; color: var(--secondary); margin-bottom: 10px;">Chuyến Đi #<?php echo $booking_id; ?></h2>
    
    <div class="status-timeline">
        <div class="step <?php echo ($status == 'Pending' || $status == 'Confirmed' || $status == 'Completed') ? 'done' : ''; ?>">
            <div class="step-icon">1</div>
            <div class="step-text">Đang tìm xe</div>
        </div>
        <div class="step <?php echo ($status == 'Confirmed' || $status == 'Completed') ? 'done' : ''; ?>">
            <div class="step-icon">2</div>
            <div class="step-text">Tài xế đang đến</div>
        </div>
        <div class="step <?php echo ($status == 'Completed') ? 'done' : ''; ?>">
            <div class="step-icon">3</div>
            <div class="step-text">Hoàn thành</div>
        </div>
    </div>

    <?php if ($status == 'Confirmed' && $booking['DriverID'] != null): ?>
    <div class="grab-driver-card">
        <div class="driver-avatar">👨‍✈️</div>
        <div class="driver-info">
            <div class="driver-name"><?php echo $booking['DriverName']; ?> ⭐ 4.9</div>
            <div class="vehicle-plate"><?php echo $booking['RegNumber']; ?></div>
            <div class="vehicle-model"><?php echo $booking['MakeModel']; ?> • <?php echo $booking['Color']; ?></div>
        </div>
        <a href="tel:<?php echo $booking['DriverPhone']; ?>" class="btn-call">📞 Gọi</a>
    </div>
    <?php elseif ($status == 'Pending'): ?>
    <div style="text-align: center; padding: 30px; background: #fff9e6; border-radius: 12px; color: #b38f00; font-weight: bold;">
        ⏳ Hệ thống đang điều phối tài xế gần nhất cho bạn...
    </div>
    <?php endif; ?>

    <div class="trip-details">
        <div class="detail-row">
            <div class="detail-label">Điểm Đón</div>
            <div class="detail-value"><?php echo $booking['PickupLocation']; ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Điểm Đến</div>
            <div class="detail-value"><?php echo $booking['Destination']; ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Thời Gian</div>
            <div class="detail-value"><?php echo date('d/m/Y H:i', strtotime($booking['PickupDateTime'])); ?></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Giá Cước</div>
            <div class="detail-value" style="color: var(--accent); font-size: 1.2rem;">$<?php echo $booking['TotalFare']; ?></div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>