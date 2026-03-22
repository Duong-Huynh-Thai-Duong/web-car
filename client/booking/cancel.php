<?php
// client/booking/cancel.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone to Vietnam to match your coursework and testing environment
date_default_timezone_set('Asia/Ho_Chi_Minh');

// --- DEMO HACK: Force login as 'duong' ---
$_SESSION['user_id'] = 1016; 
$_SESSION['name'] = 'Duong'; 
// -----------------------------------------

if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit();
}

require_once '../../config/db.php';
require_once '../../includes/functions.php'; 

$customerId = $_SESSION['user_id'];
$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = "";
$isCancelable = false;
$booking = null;

if ($bookingId > 0) {
    // 1. Fetch the specific booking, ensuring it belongs to the logged-in user
    $sql = "SELECT BookingID, PickupLocation, Destination, PickupDateTime, TotalFare, Status 
            FROM bookings 
            WHERE BookingID = ? AND CustomerID = ?";
            
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $bookingId, $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $booking = $result->fetch_assoc();
            
            // 2. US19 Business Logic: The 24-Hour Rule
            $pickupTime = new DateTime($booking['PickupDateTime']);
            $currentTime = new DateTime('now');
            
            // Calculate difference in seconds
            $timeDifference = $pickupTime->getTimestamp() - $currentTime->getTimestamp();
            $hoursUntilPickup = $timeDifference / 3600;
            
            // It is cancelable if it's more than 24 hours away AND not already cancelled/completed
            if ($hoursUntilPickup >= 24 && in_array($booking['Status'], ['Pending', 'Confirmed'])) {
                $isCancelable = true;
            }
        }
        $stmt->close();
    }
}

// 3. Handle the actual cancellation POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_cancel']) && $isCancelable) {
    $updateSql = "UPDATE bookings SET Status = 'Cancelled' WHERE BookingID = ? AND CustomerID = ?";
    if ($updateStmt = $conn->prepare($updateSql)) {
        $updateStmt->bind_param("ii", $bookingId, $customerId);
        if ($updateStmt->execute()) {
            $message = "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> Booking #{$bookingId} has been successfully cancelled.</div>";
            $booking['Status'] = 'Cancelled'; // Update local variable for UI
            $isCancelable = false; // Prevent re-cancelling
        } else {
            $message = "<div class='alert alert-danger'>Error cancelling booking. Please try again later.</div>";
        }
        $updateStmt->close();
    }
}

include '../../includes/header.php'; 
?>

<style>
    .cancel-wrapper { max-width: 600px; margin: 50px auto; background: var(--white); border-radius: 12px; box-shadow: var(--shadow); padding: 40px; text-align: center; }
    .cancel-title { color: var(--secondary); font-weight: 800; font-size: 1.8rem; margin-bottom: 20px; }
    
    .booking-summary { background-color: var(--light); padding: 20px; border-radius: 8px; text-align: left; margin: 25px 0; border-left: 4px solid var(--secondary); }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px dashed #dcece7; padding-bottom: 5px; }
    .summary-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .summary-label { color: #666; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; }
    .summary-val { color: var(--secondary); font-weight: bold; }
    
    .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; text-align: center; }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; text-align: left; font-size: 0.95rem; line-height: 1.5; }
    
    .btn-group { display: flex; gap: 15px; justify-content: center; margin-top: 30px; }
    .btn { padding: 12px 25px; border-radius: 8px; font-weight: bold; cursor: pointer; text-decoration: none; border: none; font-size: 1rem; transition: 0.3s; }
    .btn-danger { background-color: #dc3545; color: white; }
    .btn-danger:hover { background-color: #c82333; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3); }
    .btn-back { background-color: #e2e3e5; color: #383d41; }
    .btn-back:hover { background-color: #d6d8db; }
</style>

<div class="container">
    <div class="cancel-wrapper">
        <h2 class="cancel-title">Cancel Booking</h2>

        <?php if ($message) echo $message; ?>

        <?php if (!$booking): ?>
            <div class="alert alert-warning">
                <i class="fa-solid fa-triangle-exclamation"></i> Booking not found or you do not have permission to view it.
            </div>
            <a href="history.php" class="btn btn-back">Return to History</a>
        <?php else: ?>
        
            <p>Are you sure you want to cancel the following journey?</p>
            
            <div class="booking-summary">
                <div class="summary-row">
                    <span class="summary-label">Booking Reference</span>
                    <span class="summary-val">#<?php echo htmlspecialchars($booking['BookingID']); ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Route</span>
                    <span class="summary-val"><?php echo htmlspecialchars($booking['PickupLocation']); ?> to <?php echo htmlspecialchars($booking['Destination']); ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Scheduled For</span>
                    <span class="summary-val"><?php echo formatDateTime($booking['PickupDateTime']); ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Current Status</span>
                    <span class="summary-val"><?php echo getStatusBadge($booking['Status']); ?></span>
                </div>
            </div>

            <?php if ($isCancelable): ?>
                <form action="" method="POST">
                    <input type="hidden" name="confirm_cancel" value="1">
                    <div class="btn-group">
                        <a href="history.php" class="btn btn-back">Keep Booking</a>
                        <button type="submit" class="btn btn-danger"><i class="fa-solid fa-xmark"></i> Yes, Cancel Booking</button>
                    </div>
                </form>
            <?php elseif ($booking['Status'] !== 'Cancelled'): ?>
                <div class="alert alert-warning">
                    <strong>Cancellation Policy (US19):</strong> You can only cancel or amend a booking online if it is more than 24 hours prior to the journey. Since your pickup time is less than 24 hours away, please call our support hotline to discuss cancellation.
                </div>
                <a href="history.php" class="btn btn-back" style="margin-top: 20px; display: inline-block;">Return to History</a>
            <?php else: ?>
                <a href="history.php" class="btn btn-back" style="margin-top: 20px; display: inline-block;">Return to History</a>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
