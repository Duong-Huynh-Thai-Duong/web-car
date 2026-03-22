<?php
// client/booking/amend.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone to Vietnam
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
$isAmendable = false;
$booking = null;

if ($bookingId > 0) {
    // 1. Fetch the specific booking
    $sql = "SELECT BookingID, PickupLocation, Destination, PickupDateTime, PassengerCount, VehicleSizeReq, TotalFare, Status 
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
            
            $timeDifference = $pickupTime->getTimestamp() - $currentTime->getTimestamp();
            $hoursUntilPickup = $timeDifference / 3600;
            
            if ($hoursUntilPickup >= 24 && in_array($booking['Status'], ['Pending', 'Confirmed'])) {
                $isAmendable = true;
            }
        }
        $stmt->close();
    }
}

// 3. Handle the form submission to update the booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAmendable) {
    $pickup = trim($_POST['pickup']);
    $destination = trim($_POST['destination']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $passengers = (int)$_POST['passengers'];
    $vehicleSize = trim($_POST['vehicle_size']);

    $pickupDateTime = $date . ' ' . $time . ':00';

    // CAPACITY VALIDATION (Backend Security Layer)
    $vehicleCapacities = [
        '4-seater' => 4,
        '7-seater' => 7,
        'Premium' => 4,
        'Minibus' => 16
    ];
    $maxCapacity = isset($vehicleCapacities[$vehicleSize]) ? $vehicleCapacities[$vehicleSize] : 4;

    if ($passengers > $maxCapacity) {
        $message = "<div class='alert alert-danger'><i class='fa-solid fa-triangle-exclamation'></i> Amendment failed: A {$vehicleSize} can only accommodate a maximum of {$maxCapacity} passengers.</div>";
    } else {
        // Recalculate Fare (Price strictly based on vehicle)
        $basePrices = [
            '4-seater' => 40.00,
            '7-seater' => 60.00,
            'Premium' => 80.00,
            'Minibus' => 100.00
        ];
        $baseFare = isset($basePrices[$vehicleSize]) ? $basePrices[$vehicleSize] : 40.00;
        
        // Applying the 15% discount for consistency
        $discount = $baseFare * 0.15;
        $totalFare = $baseFare - $discount;

        $updateSql = "UPDATE bookings SET PickupLocation = ?, Destination = ?, PickupDateTime = ?, PassengerCount = ?, VehicleSizeReq = ?, TotalFare = ? WHERE BookingID = ? AND CustomerID = ?";
        
        if ($updateStmt = $conn->prepare($updateSql)) {
            $updateStmt->bind_param("sssisdii", $pickup, $destination, $pickupDateTime, $passengers, $vehicleSize, $totalFare, $bookingId, $customerId);
            
            if ($updateStmt->execute()) {
                $message = "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> Booking #{$bookingId} has been successfully updated.</div>";
                
                // Update local array so the UI reflects the new changes immediately
                $booking['PickupLocation'] = $pickup;
                $booking['Destination'] = $destination;
                $booking['PickupDateTime'] = $pickupDateTime;
                $booking['PassengerCount'] = $passengers;
                $booking['VehicleSizeReq'] = $vehicleSize;
                $booking['TotalFare'] = $totalFare;
            } else {
                $message = "<div class='alert alert-danger'>Error updating booking. Please try again later.</div>";
            }
            $updateStmt->close();
        }
    }
}

// Prepare existing date/time for HTML inputs
$existingDate = '';
$existingTime = '';
if ($booking) {
    $dt = new DateTime($booking['PickupDateTime']);
    $existingDate = $dt->format('Y-m-d');
    $existingTime = $dt->format('H:i');
}

include '../../includes/header.php'; 
?>

<style>
    .amend-wrapper { max-width: 800px; margin: 40px auto; background: var(--white); border-radius: 16px; box-shadow: var(--shadow); padding: 40px; }
    .amend-title { color: var(--secondary); font-weight: 800; font-size: 2rem; margin-bottom: 25px; text-align: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; }
    
    .status-banner { display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #eaeaea; }
    
    .form-row { display: flex; gap: 20px; margin-bottom: 20px; }
    .form-group { flex: 1; display: flex; flex-direction: column; }
    .form-group label { font-weight: 700; color: var(--secondary); margin-bottom: 8px; font-size: 0.95rem; }
    .form-control { padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 1rem; background: #f9f9f9; transition: 0.3s; }
    .form-control:focus { border-color: var(--primary); background: var(--white); outline: none; box-shadow: 0 0 0 3px rgba(79, 186, 151, 0.1); }
    
    .btn-group { display: flex; gap: 15px; margin-top: 30px; }
    .btn { flex: 1; padding: 15px; border-radius: 8px; font-weight: bold; cursor: pointer; text-decoration: none; text-align: center; font-size: 1.1rem; border: none; transition: 0.3s; }
    .btn-primary { background-color: var(--primary); color: var(--white); }
    .btn-primary:hover { background-color: #3da885; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(79, 186, 151, 0.4); }
    .btn-back { background-color: #e2e3e5; color: #383d41; }
    .btn-back:hover { background-color: #d6d8db; }
    
    .fare-calculator { background-color: var(--light); padding: 20px; border-left: 5px solid var(--primary); border-radius: 8px; margin: 20px 0; font-size: 1.2rem; font-weight: bold; color: var(--secondary); display: flex; justify-content: space-between; align-items: center; }
    
    .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; text-align: center; }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; text-align: left; line-height: 1.5; }
    
    @media (max-width: 768px) { .form-row { flex-direction: column; gap: 0; } .form-group { margin-bottom: 20px; } .btn-group { flex-direction: column; } }
</style>

<div class="container">
    <div class="amend-wrapper">
        <h2 class="amend-title">Amend Booking</h2>

        <?php if ($message) echo $message; ?>

        <?php if (!$booking): ?>
            <div class="alert alert-warning">Booking not found or you do not have permission to view it.</div>
            <a href="history.php" class="btn btn-back" style="display: block; width: 200px; margin: 0 auto;">Return to History</a>
        <?php else: ?>
            
            <div class="status-banner">
                <div><strong>Booking Ref:</strong> #<?php echo htmlspecialchars($booking['BookingID']); ?></div>
                <div><?php echo getStatusBadge($booking['Status']); ?></div>
            </div>

            <?php if ($isAmendable): ?>
                <form action="" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fa-solid fa-location-dot" style="color: var(--primary);"></i> Pick-up Location</label>
                            <input type="text" name="pickup" class="form-control" value="<?php echo htmlspecialchars($booking['PickupLocation']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fa-solid fa-flag-checkered" style="color: var(--primary);"></i> Destination</label>
                            <input type="text" name="destination" class="form-control" value="<?php echo htmlspecialchars($booking['Destination']); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" value="<?php echo $existingDate; ?>" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Time</label>
                            <input type="time" name="time" class="form-control" value="<?php echo $existingTime; ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Vehicle Size</label>
                            <select name="vehicle_size" id="vehicle_size" class="form-control" required onchange="updateFormLogic()">
                                <option value="4-seater" data-price="40" data-capacity="4" <?php if($booking['VehicleSizeReq'] == '4-seater') echo 'selected'; ?>>4-seater Standard (Max 4)</option>
                                <option value="7-seater" data-price="60" data-capacity="7" <?php if($booking['VehicleSizeReq'] == '7-seater') echo 'selected'; ?>>7-seater SUV (Max 7)</option>
                                <option value="Premium" data-price="80" data-capacity="4" <?php if($booking['VehicleSizeReq'] == 'Premium') echo 'selected'; ?>>Premium/Luxury (Max 4)</option>
                                <option value="Minibus" data-price="100" data-capacity="16" <?php if($booking['VehicleSizeReq'] == 'Minibus') echo 'selected'; ?>>Minibus (8-16 seats)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Passengers</label>
                            <input type="number" name="passengers" id="passengers" class="form-control" min="1" max="16" value="<?php echo $booking['PassengerCount']; ?>" required>
                        </div>
                    </div>

                    <div class="fare-calculator">
                        <span>New Estimated Total:</span>
                        <span style="color: var(--primary);">£<span id="totalOut"><?php echo number_format($booking['TotalFare'], 2); ?></span></span>
                    </div>

                    <div class="btn-group">
                        <a href="history.php" class="btn btn-back">Discard Changes</a>
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Amendments</button>
                    </div>
                </form>

            <?php else: ?>
                <div class="alert alert-warning">
                    <strong>Amendment Policy (US19):</strong> You can only amend a booking if it is more than 24 hours prior to the journey. Your pickup is scheduled for <?php echo formatDateTime($booking['PickupDateTime']); ?>. Please contact support if you need urgent assistance.
                </div>
                
                <div class="form-row" style="margin-top: 20px;">
                    <div class="form-group"><label>Route</label><div class="form-control" style="background: #eee;"><?php echo htmlspecialchars($booking['PickupLocation']) . ' &rarr; ' . htmlspecialchars($booking['Destination']); ?></div></div>
                </div>
                <div class="btn-group" style="justify-content: center;">
                    <a href="history.php" class="btn btn-back" style="flex: none; width: 200px;">Return to History</a>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<script>
    function updateFormLogic() {
        const selectElement = document.getElementById('vehicle_size');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const passengerInput = document.getElementById('passengers');
        
        // 1. Enforce Passenger Capacity
        const maxCapacity = parseInt(selectedOption.getAttribute('data-capacity'));
        passengerInput.max = maxCapacity;
        
        if (parseInt(passengerInput.value) > maxCapacity) {
            passengerInput.value = maxCapacity;
        }

        // 2. Update Pricing Display (Base Price - 15% Discount)
        let baseFare = parseFloat(selectedOption.getAttribute('data-price'));
        let discount = baseFare * 0.15;
        let total = baseFare - discount;

        document.getElementById('totalOut').innerText = total.toFixed(2);
    }
    
    // Ensure the logic runs immediately to cap the inputs based on existing DB data
    window.onload = updateFormLogic;
</script>

<?php include '../../includes/footer.php'; ?>
