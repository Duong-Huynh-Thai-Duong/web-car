<?php
session_start();
include 'config/db.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_consignment'])) {
    $customer_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $brand = $conn->real_escape_string($_POST['brand']);
    $model = $conn->real_escape_string($_POST['model']);
    $year = (int)$_POST['year'];
    $odo = $conn->real_escape_string($_POST['odo']);
    $city = $conn->real_escape_string($_POST['city']);
    $district = $conn->real_escape_string($_POST['district']);
    $availability = $conn->real_escape_string($_POST['availability']);
    $referral = $conn->real_escape_string($_POST['referral']);

    $sql = "INSERT INTO consignments (CustomerID, FullName, Phone, Brand, Model, Year, ODO, City, District, Availability, Referral) 
            VALUES ($customer_id, '$fullname', '$phone', '$brand', '$model', $year, '$odo', '$city', '$district', '$availability', '$referral')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Consignment request submitted successfully! Our team will contact you within 48 hours.');
                window.location.href = 'consignment.php';
              </script>";
        exit();
    } else {
        $msg = "<p style='color: #e74c3c; text-align: center; font-weight: bold; background: #fdf0ed; padding: 10px; border-radius: 8px;'>Error: " . $conn->error . "</p>";
    }
}
?>

<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    body { background-color: #f9fbfa; }
    .consignment-wrapper { font-family: 'Nunito', sans-serif; padding-bottom: 60px; }
    
    .login-prompt-banner { 
        background: linear-gradient(135deg, var(--secondary), #1a252f); color: white; text-align: center; 
        padding: 50px 20px; border-radius: 0 0 30px 30px; margin-bottom: 0px; box-shadow: var(--shadow); 
        max-width: 1200px; margin: 0 auto 50px auto;
    }
    .login-prompt-banner h2 { font-size: 2.2rem; font-weight: 800; margin-bottom: 15px; margin-top: 0;}
    .login-prompt-banner p { font-size: 1.1rem; color: #bdc3c7; margin-bottom: 25px; max-width: 600px; margin-left: auto; margin-right: auto; }
    .btn-white-outline { padding: 12px 30px; border: 2px solid white; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; transition: 0.3s; margin: 0 10px; display: inline-block; }
    .btn-white-outline:hover { background: white; color: var(--secondary); }
    .btn-solid-green { padding: 12px 30px; background: var(--primary); color: white; text-decoration: none; border-radius: 8px; font-weight: bold; border: 2px solid var(--primary); transition: 0.3s; margin: 0 10px; display: inline-block; }
    .btn-solid-green:hover { background: #3aa385; border-color: #3aa385; }

    .modern-form-hero {
        position: relative;
        width: 100%;
        min-height: 600px;
        background: url('https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
        display: flex;
        align-items: center;
        padding: 60px 20px;
    }
    .hero-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 30, 0.85);
        z-index: 1;
    }
    .hero-content {
        position: relative; z-index: 2;
        max-width: 1200px; margin: 0 auto;
        display: flex; align-items: center; justify-content: space-between; gap: 50px;
        width: 100%;
    }
    .hero-text { flex: 1; color: white; }
    .hero-text h1 { font-size: 3.2rem; font-weight: 800; line-height: 1.3; margin-top: 0; margin-bottom: 20px; }
    
    .hero-form-card {
        flex: 1; max-width: 550px;
        background: #ffffff; padding: 35px 40px; border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    }
    .hero-form-card h3 { color: var(--primary); font-size: 1.6rem; font-weight: 800; margin-top: 0; margin-bottom: 5px; }
    .hero-form-card > p { color: #888; font-size: 0.9rem; margin-bottom: 25px; }

    .input-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
    .full-width { grid-column: 1 / -1; }
    
    .input-group label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--secondary); margin-bottom: 6px; }
    .input-group label span { color: #e74c3c; } 
    .input-group input, .input-group select {
        width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px;
        font-family: inherit; font-size: 0.95rem; box-sizing: border-box; background: #fff; transition: 0.3s;
    }
    .input-group input:focus, .input-group select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79, 186, 151, 0.1); }
    
    .btn-submit {
        width: 100%; padding: 15px; background: var(--primary); color: white; border: none; border-radius: 8px;
        font-size: 1.1rem; font-weight: 800; cursor: pointer; transition: 0.3s; margin-top: 10px;
    }
    .btn-submit:hover { background: #3aa385; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(79, 186, 151, 0.3); }

    .info-container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
    .section-header { text-align: center; margin-bottom: 50px; margin-top: 60px; }
    .section-header h2 { color: var(--secondary); font-size: 2.2rem; font-weight: 800; margin-bottom: 15px; }
    .section-header p { color: #666; font-size: 1.1rem; max-width: 700px; margin: 0 auto; }

    .feature-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 40px; margin-bottom: 60px; }
    .feature-card { background: white; padding: 30px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); display: flex; gap: 20px; align-items: flex-start; border: 1px solid #eee; }
    .feature-icon { background: #e8f6f1; color: var(--primary); width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; flex-shrink: 0; }
    .feature-text h4 { color: var(--secondary); font-size: 1.3rem; font-weight: 800; margin-top: 0; margin-bottom: 10px; }
    .feature-text p { color: #666; font-size: 0.95rem; line-height: 1.6; margin: 0; }

    .comparison-section { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 40px; border: 1px solid #eee; }
    .comparison-table { width: 100%; border-collapse: collapse; text-align: center; }
    .comparison-table th { padding: 20px; font-size: 1.1rem; color: var(--secondary); border-bottom: 2px solid #eee; }
    .comparison-table td { padding: 18px 20px; border-bottom: 1px solid #eee; color: #555; }
    .comparison-table td:first-child { text-align: left; font-weight: 600; color: var(--secondary); }
    .check-icon { color: var(--primary); font-size: 1.2rem; }
    .cross-icon { color: #ccc; font-size: 1.2rem; }

    @media (max-width: 992px) {
        .hero-content { flex-direction: column; text-align: center; }
        .hero-text h1 { font-size: 2.5rem; }
        .feature-grid { grid-template-columns: 1fr; }
        .comparison-section { overflow-x: auto; padding: 20px; }
    }
    @media (max-width: 600px) {
        .input-grid { grid-template-columns: 1fr; }
    }
</style>

<?php

ob_start();
?>
<div class="login-prompt-banner">
    <h2>Ready to Earn Passive Income?</h2>
    <p>Log in to your HireMyCar account to easily manage your consigned vehicles, track earnings, and enjoy a seamless experience.</p>
    <a href="login.php" class="btn-solid-green">Log In Now</a>
    <a href="register.php" class="btn-white-outline">Create Account</a>
</div>
<?php
$block_login_prompt = ob_get_clean();

ob_start();
?>
<div class="modern-form-hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        
        <div class="hero-text">
            <h1>Earn passive income easily with HireMyCar!</h1>
        </div>

        <div class="hero-form-card">
            <h3>Register for car rental</h3>
            <p>We will contact you within 48 hours to complete the procedure!</p>
            
            <?php echo $msg; ?>

            <form method="POST" action="">
                <div class="input-grid">
                    <div class="input-group">
                        <label>Full Name <span>*</span></label>
                        <input type="text" name="fullname" required placeholder="Enter full name" value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''; ?>">
                    </div>
                    <div class="input-group">
                        <label>Phone Number <span>*</span></label>
                        <input type="tel" name="phone" required placeholder="Enter phone number">
                    </div>

                    <div class="input-group">
                        <label>Car Brand <span>*</span></label>
                        <select name="brand" required>
                            <option value="" disabled selected>Select Brand</option>
                            <option value="Toyota">Toyota</option>
                            <option value="Honda">Honda</option>
                            <option value="Hyundai">Hyundai</option>
                            <option value="Kia">Kia</option>
                            <option value="Mazda">Mazda</option>
                            <option value="Vinfast">Vinfast</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Car Model <span>*</span></label>
                        <input type="text" name="model" required placeholder="e.g. Mazda 3">
                    </div>

                    <div class="input-group">
                        <label>Year <span>*</span></label>
                        <select name="year" required>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                            <option value="2022">2022</option>
                            <option value="2021">2021</option>
                            <option value="2020">2020</option>
                            <option value="2019">2019</option>
                            <option value="2018">2018 or Older</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>ODO (Mileage) <span>*</span></label>
                        <select name="odo" required>
                            <option value="" disabled selected>Select ODO</option>
                            <option value="0 - 10,000 km">0 - 10,000 km</option>
                            <option value="10,000 - 30,000 km">10,000 - 30,000 km</option>
                            <option value="30,000 - 50,000 km">30,000 - 50,000 km</option>
                            <option value="Over 50,000 km">Over 50,000 km</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>City <span>*</span></label>
                        <select name="city" required>
                            <option value="" disabled selected>Select City</option>
                            <option value="Ho Chi Minh City">Ho Chi Minh City</option>
                            <option value="Hanoi">Hanoi</option>
                            <option value="Da Nang">Da Nang</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>District <span>*</span></label>
                        <input type="text" name="district" required placeholder="Enter district">
                    </div>

                    <div class="input-group full-width">
                        <label>Available days for rent per month <span>*</span></label>
                        <select name="availability" required>
                            <option value="Mostly available">Mostly available</option>
                            <option value="Partially available">Partially available</option>
                            <option value="Weekends only">Weekends only</option>
                        </select>
                    </div>

                    <div class="input-group full-width">
                        <label>Referral info (optional)</label>
                        <input type="text" name="referral" placeholder="Enter referral phone or code">
                    </div>
                </div>
                
                <button type="submit" name="submit_consignment" class="btn-submit">Register Now</button>
            </form>
        </div>

    </div>
</div>
<?php
$block_form = ob_get_clean();

ob_start();
?>
<div class="info-container">
    <div class="section-header">
        <h2>Why Consign with HireMyCar?</h2>
        <p>We provide a comprehensive, secure, and highly profitable solution for car owners.</p>
    </div>
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
            <div class="feature-text">
                <h4>Absolute Peace of Mind</h4>
                <p>HireMyCar handles everything. We strictly verify renters' profiles, process contracts, and manage legal issues to ensure your car is always in safe hands.</p>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fa-solid fa-mobile-screen"></i></div>
            <div class="feature-text">
                <h4>Smart Anti-Theft Tech</h4>
                <p>Equipped with GPS tracking and smart engine-lock systems. You can monitor your vehicle's location 24/7 directly from your smartphone.</p>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fa-solid fa-headset"></i></div>
            <div class="feature-text">
                <h4>Comprehensive A-Z Support</h4>
                <p>From finding customers and handing over the keys to regular maintenance tracking and handling traffic fines. We save you time and effort.</p>
            </div>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="feature-text">
                <h4>Full Control & Flexibility</h4>
                <p>You have full control over your vehicle. Block dates when you need to use the car for personal trips. Zero pressure, pure flexibility.</p>
            </div>
        </div>
    </div>

    <div class="comparison-section">
        <h2 style="text-align: center; color: var(--secondary); margin-bottom: 30px;">HireMyCar: The Superior Solution</h2>
        <table class="comparison-table">
            <thead>
                <tr>
                    <th>Benefits</th>
                    <th>HireMyCar</th>
                    <th>Self-Management</th>
                    <th>Other Platforms</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Save 90% of time and effort</td>
                    <td><i class="fa-solid fa-check check-icon"></i></td>
                    <td><i class="fa-solid fa-xmark cross-icon"></i></td>
                    <td><i class="fa-solid fa-xmark cross-icon"></i></td>
                </tr>
                <tr>
                    <td>Strict 10-step risk management</td>
                    <td><i class="fa-solid fa-check check-icon"></i></td>
                    <td><i class="fa-solid fa-xmark cross-icon"></i></td>
                    <td><i class="fa-solid fa-xmark cross-icon"></i></td>
                </tr>
                <tr>
                    <td>Free safety device installation</td>
                    <td><i class="fa-solid fa-check check-icon"></i></td>
                    <td><i class="fa-solid fa-xmark cross-icon"></i></td>
                    <td><i class="fa-solid fa-xmark cross-icon"></i></td>
                </tr>
                <tr>
                    <td>Handling traffic fines on behalf of owner</td>
                    <td><i class="fa-solid fa-check check-icon"></i></td>
                    <td><i class="fa-solid fa-xmark cross-icon"></i></td>
                    <td><i class="fa-solid fa-xmark cross-icon"></i></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php
$block_features = ob_get_clean();

echo "<div class='consignment-wrapper'>";

if (isset($_SESSION['user_id'])) {
    echo $block_form;
    echo $block_features;
} else {
    echo $block_login_prompt;
    echo $block_features;
    echo $block_form;
}

echo "</div>";
?>

<?php include 'includes/footer.php'; ?>