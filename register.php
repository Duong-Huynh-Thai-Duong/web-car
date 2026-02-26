<?php
session_start();
include 'config/db.php';

if (isset($_POST['action']) && $_POST['action'] == 'send_phone_otp') {
    $phone = $_POST['phone'];
    $otp = rand(100000, 999999);
    $_SESSION['phone_otp'] = $otp;
    $_SESSION['otp_phone_number'] = $phone;

    echo "success|" . $otp;
    exit();
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $entered_otp = $_POST['entered_otp']; 
    $citizen_id = $conn->real_escape_string($_POST['citizen_id']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!isset($_SESSION['phone_otp']) || $entered_otp != $_SESSION['phone_otp'] || $phone != $_SESSION['otp_phone_number']) {
        $msg = "<p style='color: #e74c3c; text-align: center; font-weight: bold; padding: 12px; background: #fdf0ed; border-radius: 8px; margin-bottom: 20px;'>Invalid Phone Verification Code!</p>";
    } 
    elseif ($password !== $confirm_password) {
        $msg = "<p style='color: #e74c3c; text-align: center; font-weight: bold; padding: 12px; background: #fdf0ed; border-radius: 8px; margin-bottom: 20px;'>Passwords do not match!</p>";
    } 
    else {
        $check_exist = $conn->query("SELECT * FROM Users WHERE Email = '$email' OR PhoneNumber = '$phone' OR CitizenID = '$citizen_id'");
        if ($check_exist->num_rows > 0) {
            $msg = "<p style='color: #e74c3c; text-align: center; font-weight: bold; padding: 12px; background: #fdf0ed; border-radius: 8px; margin-bottom: 20px;'>Account info already exists!</p>";
        } else {
            $sql = "INSERT INTO Users (FullName, Email, PasswordHash, PhoneNumber, CitizenID, Role) 
                    VALUES ('$fullname', '$email', '$password', '$phone', '$citizen_id', 'Customer')";
            
            if ($conn->query($sql) === TRUE) {
                unset($_SESSION['phone_otp']);
                unset($_SESSION['otp_phone_number']);
                echo "<script>alert('Registration successful!'); window.location.href = 'login.php';</script>";
                exit();
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div style="max-width: 650px; margin: 50px auto; padding: 0 20px; font-family: 'Nunito', sans-serif;">
    
    <div style="height: 250px; margin-bottom: 30px; border-radius: 20px; overflow: hidden; box-shadow: var(--shadow);">
        <img src="assets/images/register-banner.jpg" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=1200&q=80'">
    </div>

    <div style="padding: 50px 45px; background: #fff; border-radius: 20px; box-shadow: 0 15px 45px rgba(0,0,0,0.07);">
        <h2 style="text-align: center; color: var(--secondary); margin-bottom: 35px; font-weight: 800; font-size: 2rem;">Create an Account</h2>
        
        <?php echo $msg; ?>
        
        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label style="font-weight: 700; display: block; margin-bottom: 10px; color: var(--secondary);">Full Name</label>
                <input type="text" name="fullname" required placeholder="Enter your full name" style="width: 100%; padding: 14px 18px; border: 1px solid #e2e8f0; border-radius: 10px; box-sizing: border-box; font-size: 1rem; background: #f8fafc;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: 700; display: block; margin-bottom: 10px; color: var(--secondary);">Email Address</label>
                <input type="email" name="email" required placeholder="example@hiremycar.com" style="width: 100%; padding: 14px 18px; border: 1px solid #e2e8f0; border-radius: 10px; box-sizing: border-box; font-size: 1rem; background: #f8fafc;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: 700; display: block; margin-bottom: 10px; color: var(--secondary);">Phone Number</label>
                <div style="display: flex; gap: 12px;">
                    <input type="tel" id="phone" name="phone" required placeholder="10-digit phone number" style="flex: 1; padding: 14px 18px; border: 1px solid #e2e8f0; border-radius: 10px; box-sizing: border-box; font-size: 1rem; background: #f8fafc;">
                    <button type="button" id="btn-send-otp" style="padding: 0 25px; background: var(--secondary); color: #fff; border: none; border-radius: 10px; font-weight: 800; cursor: pointer; transition: 0.3s;">Send Code</button>
                </div>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="font-weight: 700; color: var(--primary); display: block; margin-bottom: 10px;">Phone Verification Code</label>
                <input type="text" name="entered_otp" required placeholder="Enter 6-digit code" maxlength="6" style="width: 100%; padding: 16px; border: 2px dashed var(--primary); border-radius: 12px; text-align: center; font-weight: 800; letter-spacing: 8px; font-size: 1.2rem; background: #f0fdfa; color: var(--secondary); box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: 700; display: block; margin-bottom: 10px; color: var(--secondary);">Citizen ID / Passport</label>
                <input type="text" name="citizen_id" required placeholder="12-digit number" style="width: 100%; padding: 14px 18px; border: 1px solid #e2e8f0; border-radius: 10px; box-sizing: border-box; font-size: 1rem; background: #f8fafc;">
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 35px;">
                <div style="flex: 1;">
                    <label style="font-weight: 700; display: block; margin-bottom: 10px; color: var(--secondary);">Password</label>
                    <input type="password" name="password" required style="width: 100%; padding: 14px 18px; border: 1px solid #e2e8f0; border-radius: 10px; box-sizing: border-box;">
                </div>
                <div style="flex: 1;">
                    <label style="font-weight: 700; display: block; margin-bottom: 10px; color: var(--secondary);">Confirm Password</label>
                    <input type="password" name="confirm_password" required style="width: 100%; padding: 14px 18px; border: 1px solid #e2e8f0; border-radius: 10px; box-sizing: border-box;">
                </div>
            </div>

            <button type="submit" name="register" style="width: 100%; padding: 18px; background: var(--primary); color: #fff; border: none; border-radius: 12px; font-weight: 800; font-size: 1.2rem; cursor: pointer; box-shadow: 0 4px 12px rgba(79, 186, 151, 0.3); transition: 0.3s;">Complete Registration</button>
        </form>

        <p style="text-align: center; margin-top: 30px; color: #64748b; font-weight: 600;">
            Already have an account? <a href="login.php" style="color: var(--primary); font-weight: 800; text-decoration: none;">Log in</a>
        </p>
    </div>
</div>

<script>
    document.getElementById('btn-send-otp').addEventListener('click', function() {
        var phone = document.getElementById('phone').value;
        if(phone === '') { alert('Please enter phone number first'); return; }
        
        var btn = this;
        var originalText = btn.innerText;
        btn.innerText = 'Sending...';
        btn.disabled = true;

        var formData = new FormData();
        formData.append('action', 'send_phone_otp');
        formData.append('phone', phone);

        fetch('register.php', { method: 'POST', body: formData })
        .then(response => response.text())
        .then(data => {
            var res = data.split('|');
            if(res[0].trim() === 'success') {
                alert('DEVELOPER TEST - OTP CODE: ' + res[1]); 
                btn.innerText = 'Sent ✓';
                btn.style.background = '#27ae60';
            } else {
                alert('Error sending code.');
                btn.innerText = originalText;
                btn.disabled = false;
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>