<?php
session_start();
include 'config/db.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_car'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $customer_id = $_SESSION['user_id'];
    $pickup = $conn->real_escape_string($_POST['pickup_location']);
    $destination = $conn->real_escape_string($_POST['destination']);
    $pickup_time = $_POST['pickup_datetime'];
    $passengers = (int)$_POST['passengers'];
    $car_type = $_POST['car_type'];
    $payment = $_POST['payment_method'];

    $base_fare = 50;
    if ($car_type == '7 Seats') $base_fare = 80;
    if ($car_type == 'VIP') $base_fare = 150;
    $total_fare = ($base_fare * 0.85) * 1.1; 

    $sql = "INSERT INTO bookings (CustomerID, PickupLocation, Destination, PickupDateTime, PassengerCount, VehicleSizeReq, TotalFare, PaymentMethod, Status) 
            VALUES ($customer_id, '$pickup', '$destination', '$pickup_time', $passengers, '$car_type', $total_fare, '$payment', 'Completed')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Booking successful! Enjoy your trip.'); window.location.href = 'history.php';</script>";
        exit();
    } else {
        $msg = "<p style='color: #e74c3c; text-align: center; font-weight: bold;'>Error: " . $conn->error . "</p>";
    }
}
?>

<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    body { background-color: #f9fbfa; }
    .booking-wrapper { font-family: 'Nunito', sans-serif; padding-bottom: 60px; }
    
    .login-prompt-banner { 
        background: linear-gradient(135deg, var(--secondary), #1a252f); color: white; text-align: center; 
        padding: 50px 20px; border-radius: 0 0 30px 30px; margin-bottom: 50px; box-shadow: var(--shadow); 
        max-width: 1200px; margin: 0 auto 50px auto;
    }
    .btn-solid-green { padding: 12px 30px; background: var(--primary); color: white; text-decoration: none; border-radius: 8px; font-weight: bold; transition: 0.3s; display: inline-block; }
    .btn-solid-green:hover { background: #3aa385; transform: translateY(-2px); }

    .btn-white-outline { 
        padding: 12px 30px; border: 2px solid white; color: white; text-decoration: none; border-radius: 8px; 
        font-weight: bold; transition: 0.3s; display: inline-block; 
    }
    .btn-white-outline:hover { background: white; color: var(--secondary); transform: translateY(-2px); }

    .booking-hero {
        position: relative; width: 100%; min-height: 600px;
        background: url('https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
        display: flex; align-items: center; padding: 60px 20px;
    }
    .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 30, 0.85); z-index: 1; }
    .hero-content { position: relative; z-index: 2; max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 50px; width: 100%; }
    .hero-text { flex: 1; color: white; }
    .hero-text h1 { font-size: 3.5rem; font-weight: 800; line-height: 1.2; margin: 0; }

    .hero-form-card { flex: 1; max-width: 500px; background: #fff; padding: 35px 40px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
    .hero-form-card h3 { color: var(--primary); font-size: 1.6rem; font-weight: 800; margin-top: 0; margin-bottom: 20px; }

    .input-group { margin-bottom: 15px; }
    .input-group label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--secondary); margin-bottom: 6px; }
    .input-group input, .input-group select { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 0.95rem; box-sizing: border-box; }
    
    .price-preview { background: #f4fdf9; border: 1px dashed var(--primary); border-radius: 12px; padding: 15px; margin-bottom: 15px; }
    .price-row { display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 5px; }
    .total-row { border-top: 1px solid #eee; margin-top: 10px; padding-top: 10px; font-weight: 800; color: var(--primary); font-size: 1.1rem; }

    .btn-submit { width: 100%; padding: 16px; background: var(--primary); color: white; border: none; border-radius: 10px; font-weight: 800; font-size: 1.1rem; cursor: pointer; transition: 0.3s; }
    .btn-submit:hover { background: #3aa385; transform: translateY(-2px); }

    .info-container { max-width: 1200px; margin: 0 auto; padding: 50px 20px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
    .info-card { background: white; padding: 30px; border-radius: 16px; border: 1px solid #eee; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .info-card h3 { color: var(--secondary); font-size: 1.3rem; margin-top: 0; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .amenities-list { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; list-style: none; padding: 0; }
    .amenities-list li { font-size: 0.9rem; color: #555; }
    .amenities-list li i { color: var(--primary); margin-right: 8px; }

    @media (max-width: 992px) { .hero-content { flex-direction: column; text-align: center; } .info-grid { grid-template-columns: 1fr; } }
</style>

<div class="booking-wrapper">

<?php
ob_start(); ?>
<div class="login-prompt-banner">
    <h2>Explore Your Perfect Journey</h2>
    <p>Sign in to your HireMyCar account to book premium vehicles and manage your trips with ease.</p>
    <div style="display: flex; justify-content: center; gap: 15px; margin-top: 20px;">
        <a href="login.php" class="btn-solid-green">Login to Start Booking</a>
        <a href="register.php" class="btn-white-outline">Register Now</a>
    </div>
</div>
<?php $block_guest = ob_get_clean(); ?>

<?php
ob_start(); ?>
<div class="booking-hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-text">
            <h1>Your Journey,<br>Your Way.</h1>
            <p style="font-size: 1.2rem; color: #ccc; margin-top: 20px;">Book a premium car in seconds and explore the world.</p>
        </div>
        <div class="hero-form-card">
            <h3>Start Your Trip</h3>
            <?php echo $msg; ?>
            <form method="POST" action="">
                <div class="input-group">
                    <label>Pickup Location</label>
                    <input type="text" name="pickup_location" required placeholder="e.g. Tan Son Nhat Airport">
                </div>
                <div class="input-group">
                    <label>Destination</label>
                    <input type="text" name="destination" required placeholder="Where are you going?">
                </div>

                <div class="input-group">
                    <label>Car Brand</label>
                    <select name="car_brand" required>
                        <option value="" disabled selected>Select a brand</option>
                        <option value="VINFAST">VINFAST</option>
                        <option value="MITSUBISHI">MITSUBISHI</option>
                        <option value="TOYOTA">TOYOTA</option>
                        <option value="HYUNDAI">HYUNDAI</option>
                        <option value="KIA">KIA</option>
                        <option value="MG">MG</option>
                        <option value="MAZDA">MAZDA</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px;">
                    <div class="input-group" style="flex: 2;">
                        <label>Pickup Date & Time</label>
                        <input type="datetime-local" name="pickup_datetime" required>
                    </div>
                    <div class="input-group" style="flex: 1;">
                        <label>Passengers</label>
                        <input type="number" name="passengers" value="1" min="1" max="16">
                    </div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <div class="input-group" style="flex: 1;">
                        <label>Car Type</label>
                        <select name="car_type" id="carSelect">
                            <option value="4 Seats">4 Seats</option>
                            <option value="7 Seats">7 Seats</option>
                            <option value="VIP">VIP (Luxury)</option>
                        </select>
                    </div>
                    <div class="input-group" style="flex: 1;">
                        <label>Payment</label>
                        <select name="payment_method">
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>
                </div>
                <div class="price-preview">
                    <div class="price-row"><span>Base Rate:</span><span id="p-base">$50.00</span></div>
                    <div class="price-row" style="color: #e74c3c;"><span>Online Discount (15%):</span><span id="p-disc">-$7.50</span></div>
                    <div class="price-row total-row"><span>Total Payment:</span><span id="p-total">$46.75</span></div>
                </div>
                <button type="submit" name="book_car" class="btn-submit">Confirm & Book Now</button>
            </form>
        </div>
    </div>
</div>
<?php $block_form = ob_get_clean(); ?>

<?php
// --- ĐÓNG GÓI THÔNG TIN CƠ BẢN ---
ob_start(); ?>
<div class="info-container">
    <h2 style="text-align: center; color: var(--secondary); margin-bottom: 40px;">Booking Information</h2>
    <div class="info-grid">
        <div class="info-card" style="grid-column: 1/-1;">
            <h3><i class="fa-solid fa-car"></i> Vehicle Amenities</h3>
            <ul class="amenities-list">
                <li><i class="fa-solid fa-check"></i> Bluetooth Connectivity</li>
                <li><i class="fa-solid fa-check"></i> 360° Camera & Dashcam</li>
                <li><i class="fa-solid fa-check"></i> GPS Navigation</li>
                <li><i class="fa-solid fa-check"></i> Panoramic Sunroof</li>
                <li><i class="fa-solid fa-check"></i> USB Charging Ports</li>
                <li><i class="fa-solid fa-check"></i> Advanced Airbag System</li>
            </ul>
        </div>
        <div class="info-card">
            <h3><i class="fa-solid fa-file-invoice-dollar"></i> Potential Extra Fees</h3>
            <p style="font-size: 0.85rem; color: #777; margin-bottom: 10px;">Late Return Fee: $10 / hour</p>
            <p style="font-size: 0.85rem; color: #777; margin-bottom: 10px;">Fuel Surcharge: $2 / liter</p>
            <p style="font-size: 0.85rem; color: #777;">Cleaning Fee: $15.00</p>
        </div>
        <div class="info-card">
            <h3><i class="fa-solid fa-scale-balanced"></i> Rental Terms</h3>
            <ul style="font-size: 0.85rem; color: #555; padding-left: 20px;">
                <li>No smoking inside the vehicle.</li>
                <li>Renter is responsible for traffic fines.</li>
                <li>Valid driving license required at pickup.</li>
            </ul>
        </div>
    </div>
</div>
<?php $block_info = ob_get_clean(); ?>

<?php
// --- LOGIC HIỂN THỊ ---
if (isset($_SESSION['user_id'])) {
    echo $block_form; echo $block_info;
} else {
    echo $block_guest; echo $block_info; echo $block_form;
}
?>

</div>

<script>
    document.getElementById('carSelect').addEventListener('change', function() {
        let val = this.value; let base = 50;
        if(val === '7 Seats') base = 80; if(val === 'VIP') base = 150;
        let disc = base * 0.15; let total = (base - disc) * 1.1;
        document.getElementById('p-base').innerText = '$' + base.toFixed(2);
        document.getElementById('p-disc').innerText = '-$' + disc.toFixed(2);
        document.getElementById('p-total').innerText = '$' + total.toFixed(2);
    });
</script>

<?php include 'includes/footer.php'; ?>