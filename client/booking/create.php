<?php
// client/booking/create.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- DEMO HACK: Force login as 'duong' ---
$_SESSION['user_id'] = 1016; 
$_SESSION['name'] = 'Duong'; 
// --------------------------------------------------------------

if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php"); 
    exit();
}

require_once '../../config/db.php';

$message = "";
$customerId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup = trim($_POST['pickup']);
    $destination = trim($_POST['destination']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $passengers = (int)$_POST['passengers'];
    $vehicleSize = trim($_POST['vehicle_size']); 
    $paymentMethod = $_POST['payment_method'];

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
        $message = "<div class='alert alert-danger'><i class='fa-solid fa-triangle-exclamation'></i> Booking failed: A {$vehicleSize} can only accommodate a maximum of {$maxCapacity} passengers.</div>";
    } else {
        // Backend Fare Calculation
        $basePrices = [
            '4-seater' => 40.00,
            '7-seater' => 60.00,
            'Premium' => 80.00,
            'Minibus' => 100.00
        ];
        $baseFare = isset($basePrices[$vehicleSize]) ? $basePrices[$vehicleSize] : 40.00;
        
        // FR03: 15% Online Discount
        $discount = $baseFare * 0.15;
        $totalFare = $baseFare - $discount;

        // Insert into Database
        $sql = "INSERT INTO bookings (CustomerID, PickupLocation, Destination, PickupDateTime, PassengerCount, VehicleSizeReq, TotalFare, PaymentMethod, Status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
                
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("isssisds", $customerId, $pickup, $destination, $pickupDateTime, $passengers, $vehicleSize, $totalFare, $paymentMethod);
            
            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'><i class='fa-solid fa-circle-check'></i> Booking successfully confirmed! A 15% online discount was applied. View it in 'My Bookings'.</div>";
            } else {
                $message = "<div class='alert alert-danger'><i class='fa-solid fa-triangle-exclamation'></i> Error processing booking: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        }
    }
}

include '../../includes/header.php'; 
?>

<style>
    .booking-wrapper { max-width: 800px; margin: 40px auto; background: var(--white); border-radius: 16px; box-shadow: var(--shadow); padding: 40px; }
    .booking-title { color: var(--secondary); font-weight: 800; font-size: 2rem; margin-bottom: 25px; text-align: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px; }
    .form-row { display: flex; gap: 20px; margin-bottom: 20px; }
    .form-group { flex: 1; display: flex; flex-direction: column; }
    .form-group label { font-weight: 700; color: var(--secondary); margin-bottom: 8px; font-size: 0.95rem; }
    
    .form-control { padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 1rem; background: #f9f9f9; transition: 0.3s; }
    .form-control:focus { border-color: var(--primary); background: var(--white); outline: none; box-shadow: 0 0 0 3px rgba(79, 186, 151, 0.1); }
    
    .fare-calculator { background-color: var(--light); padding: 20px; border-left: 5px solid var(--primary); border-radius: 8px; margin: 30px 0; }
    .fare-calculator p { margin: 5px 0; color: var(--secondary); font-size: 1.05rem; display: flex; justify-content: space-between; }
    .discount-text { color: var(--primary); font-weight: bold; }
    .total-text { font-size: 1.4rem !important; font-weight: 800; border-top: 1px solid #dcece7; padding-top: 10px; margin-top: 10px !important; }
    
    .btn-submit { width: 100%; padding: 15px; background-color: var(--primary); color: var(--white); border: none; border-radius: 8px; font-size: 1.2rem; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .btn-submit:hover { background-color: #3da885; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(79, 186, 151, 0.4); }
    
    .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; text-align: center; }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    
    @media (max-width: 768px) { .form-row { flex-direction: column; gap: 0; } .form-group { margin-bottom: 20px; } }
</style>

<div class="container">
    <div class="booking-wrapper">
        <h2 class="booking-title">Schedule Your Ride</h2>
        
        <?php if ($message) echo $message; ?>

        <form action="" method="POST" id="bookingForm">
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-location-dot" style="color: var(--primary);"></i> Pick-up Location</label>
                    <input type="text" name="pickup" class="form-control" required placeholder="e.g., Tan Son Nhat Airport">
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-flag-checkered" style="color: var(--primary);"></i> Destination</label>
                    <input type="text" name="destination" class="form-control" required placeholder="e.g., District 1">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label>Time</label>
                    <input type="time" name="time" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Vehicle Size</label>
                    <select name="vehicle_size" id="vehicle_size" class="form-control" required onchange="updateFormLogic()">
                        <option value="4-seater" data-price="40" data-capacity="4">4-seater Standard (Max 4)</option>
                        <option value="7-seater" data-price="60" data-capacity="7">7-seater SUV (Max 7)</option>
                        <option value="Premium" data-price="80" data-capacity="4">Premium/Luxury (Max 4)</option>
                        <option value="Minibus" data-price="100" data-capacity="16">Minibus (8-16 seats)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Passengers</label>
                    <input type="number" name="passengers" id="passengers" class="form-control" min="1" max="4" value="1" required>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 10px;">
                <label>Payment Method</label>
                <select name="payment_method" class="form-control" required>
                    <option value="Cash">Cash to Driver</option>
                    <option value="Card">Credit/Debit Card (Visa/Mastercard)</option>
                    <option value="PayPal">PayPal</option>
                </select>
            </div>

            <div class="fare-calculator">
                <p>Base Fare Estimate: <span>£<span id="baseOut">40.00</span></span></p>
                <p class="discount-text">Online Booking Discount (15%): <span>-£<span id="discOut">6.00</span></span></p>
                <p class="total-text">Final Estimated Total: <span style="color: var(--primary);">£<span id="totalOut">34.00</span></span></p>
            </div>

            <button type="submit" class="btn-submit">Confirm Booking</button>
        </form>
    </div>
</div>

<script>
    function updateFormLogic() {
        const selectElement = document.getElementById('vehicle_size');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const passengerInput = document.getElementById('passengers');
        
        // 1. Enforce Passenger Capacity (Frontend UX Layer)
        const maxCapacity = parseInt(selectedOption.getAttribute('data-capacity'));
        passengerInput.max = maxCapacity; // Set HTML max attribute
        
        // If the user currently typed 6, but switches to a 4-seater, auto-reduce it to 4
        if (parseInt(passengerInput.value) > maxCapacity) {
            passengerInput.value = maxCapacity;
        }

        // 2. Update Pricing Display
        let baseFare = parseFloat(selectedOption.getAttribute('data-price'));
        let discount = baseFare * 0.15;
        let total = baseFare - discount;

        document.getElementById('baseOut').innerText = baseFare.toFixed(2);
        document.getElementById('discOut').innerText = discount.toFixed(2);
        document.getElementById('totalOut').innerText = total.toFixed(2);
    }
    
    // Run once on page load to ensure initial state is correct
    window.onload = updateFormLogic;
</script>

<?php include '../../includes/footer.php'; ?>
