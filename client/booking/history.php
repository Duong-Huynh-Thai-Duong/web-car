<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- DEMO HACK: Force login as 'duong' for the presentation ---
$_SESSION['user_id'] = 1016; 
$_SESSION['name'] = 'Duong'; 
// --------------------------------------------------------------

if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit();
}

require_once '../../config/db.php';
require_once '../../includes/functions.php'; // Include our new functions

$customerId = $_SESSION['user_id'];

// Fetch all bookings for this user, ordered by newest first
$sql = "SELECT BookingID, PickupLocation, Destination, PickupDateTime, VehicleSizeReq, TotalFare, Status 
        FROM bookings 
        WHERE CustomerID = ? 
        ORDER BY PickupDateTime DESC";

$bookings = [];
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    $stmt->close();
}

include '../../includes/header.php'; 
?>

<style>
    .history-wrapper { max-width: 1000px; margin: 40px auto; padding: 20px; }
    .history-title { color: var(--secondary); font-weight: 800; font-size: 2rem; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 15px; }
    
    .booking-card { background: var(--white); border-radius: 12px; box-shadow: var(--shadow); margin-bottom: 20px; padding: 25px; display: flex; flex-direction: column; gap: 15px; transition: transform 0.2s; border-left: 5px solid var(--primary); }
    .booking-card:hover { transform: translateX(5px); }
    
    .booking-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #ddd; padding-bottom: 10px; }
    .booking-id { font-weight: 800; color: var(--secondary); font-size: 1.1rem; }
    
    .booking-body { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 10px; }
    .info-group { display: flex; flex-direction: column; }
    .info-label { font-size: 0.85rem; color: #888; font-weight: 600; text-transform: uppercase; margin-bottom: 5px; }
    .info-value { font-size: 1rem; color: var(--text-dark); font-weight: 600; }
    .route-text { display: flex; align-items: center; gap: 10px; }
    
    .booking-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; }
    
    /* Action Buttons */
    .btn-action { padding: 8px 15px; border-radius: 6px; font-weight: bold; text-decoration: none; font-size: 0.9rem; transition: 0.3s; cursor: pointer; border: none; }
    .btn-rebook { background-color: var(--light); color: var(--primary); border: 1px solid var(--primary); }
    .btn-rebook:hover { background-color: var(--primary); color: var(--white); }
    .btn-review { background-color: #FFC107; color: #000; }
    .btn-review:hover { background-color: #e0a800; }
    .btn-cancel { background-color: #f8d7da; color: #721c24; }
    .btn-cancel:hover { background-color: #dc3545; color: white; }
    
    .empty-state { text-align: center; padding: 50px; color: #888; background: var(--white); border-radius: 12px; box-shadow: var(--shadow); }
    
    @media (max-width: 768px) { .booking-body { grid-template-columns: 1fr; } }
</style>

<div class="container">
    <div class="history-wrapper">
        <h2 class="history-title"><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i> My Booking History</h2>

        <?php if (empty($bookings)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-car-side" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                <h3>No bookings found</h3>
                <p>You haven't made any journey requests yet.</p>
                <a href="create.php" class="btn-action btn-rebook" style="display: inline-block; margin-top: 15px;">Book a Ride Now</a>
            </div>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <span class="booking-id">Booking #<?php echo htmlspecialchars($booking['BookingID']); ?></span>
                        <?php echo getStatusBadge($booking['Status']); ?>
                    </div>
                    
                    <div class="booking-body">
                        <div class="info-group">
                            <span class="info-label">Journey Details</span>
                            <div class="info-value route-text">
                                <i class="fa-solid fa-location-dot" style="color: #e74c3c;"></i> <?php echo htmlspecialchars($booking['PickupLocation']); ?>
                            </div>
                            <div style="margin: 5px 0 5px 6px; border-left: 2px dashed #ccc; height: 15px;"></div>
                            <div class="info-value route-text">
                                <i class="fa-solid fa-flag-checkered" style="color: var(--primary);"></i> <?php echo htmlspecialchars($booking['Destination']); ?>
                            </div>
                        </div>
                        
                        <div class="info-group">
                            <span class="info-label">Schedule & Vehicle</span>
                            <div class="info-value"><i class="fa-regular fa-calendar" style="color: #888;"></i> <?php echo formatDateTime($booking['PickupDateTime']); ?></div>
                            <div class="info-value" style="margin-top: 10px;"><i class="fa-solid fa-car" style="color: #888;"></i> <?php echo htmlspecialchars($booking['VehicleSizeReq']); ?></div>
                        </div>
                        
                        <div class="info-group">
                            <span class="info-label">Total Cost</span>
                            <div class="info-value" style="font-size: 1.3rem; color: var(--primary);">
                                <?php echo formatCurrency($booking['TotalFare']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="booking-actions">
                        <a href="create.php?pickup=<?php echo urlencode($booking['PickupLocation']); ?>&dest=<?php echo urlencode($booking['Destination']); ?>" class="btn-action btn-rebook"><i class="fa-solid fa-rotate-right"></i> Re-book Route</a>
                        
                        <?php if ($booking['Status'] === 'Pending'): ?>
                            <a href="amend.php?id=<?php echo $booking['BookingID']; ?>" class="btn-action btn-rebook"><i class="fa-solid fa-pen"></i> Amend</a>
                            <a href="cancel.php?id=<?php echo $booking['BookingID']; ?>" class="btn-action btn-cancel"><i class="fa-solid fa-xmark"></i> Cancel</a>
                        <?php elseif ($booking['Status'] === 'Completed'): ?>
                            <button class="btn-action btn-review"><i class="fa-solid fa-star"></i> Leave Review</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<?php include '../../includes/footer.php'; ?>
