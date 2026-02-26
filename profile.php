<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

if (isset($_POST['action']) && $_POST['action'] == 'send_otp') {
    $otp = rand(100000, 999999);
    $_SESSION['profile_otp'] = $otp;
    echo "success|" . $otp;
    exit();
}

if (isset($_POST['delete_account'])) {
    $entered_otp = $_POST['delete_otp'];
    if (isset($_SESSION['profile_otp']) && $entered_otp == $_SESSION['profile_otp']) {
        $conn->query("DELETE FROM Users WHERE UserID = '$user_id'");
        session_destroy();
        echo "<script>alert('Account deleted successfully.'); window.location.href='index.php';</script>";
        exit();
    } else {
        $msg = "<p class='error-msg'>Invalid OTP for account deletion!</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $new_email = $conn->real_escape_string($_POST['email']);
    $new_phone = $conn->real_escape_string($_POST['phone']);
    $entered_otp = $_POST['otp_verify'] ?? '';
    
    $current_data = $conn->query("SELECT Email, PhoneNumber FROM Users WHERE UserID = '$user_id'")->fetch_assoc();
    $needs_otp = ($new_email !== $current_data['Email'] || $new_phone !== $current_data['PhoneNumber']);
    
    if ($needs_otp && (!isset($_SESSION['profile_otp']) || $entered_otp != $_SESSION['profile_otp'])) {
        $msg = "<p class='error-msg'>OTP verification required to change Email or Phone!</p>";
    } else {
        $avatar_sql = "";
        if (!empty($_FILES['avatar']['name'])) {
            $target_dir = "uploads/avatars/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $file_ext = pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
            $file_name = $user_id . "_" . time() . "." . $file_ext;
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                $avatar_sql = ", Avatar = '$target_file'";
            }
        }

        $pass_sql = "";
        if (!empty($_POST['new_password'])) {
            if ($_POST['new_password'] === $_POST['confirm_password']) {
                $new_pass = $_POST['new_password'];
                $pass_sql = ", PasswordHash = '$new_pass'";
            } else {
                $msg = "<p class='error-msg'>Passwords do not match!</p>";
            }
        }

        if (empty($msg)) {
            $sql = "UPDATE Users SET FullName = '$fullname', Email = '$new_email', PhoneNumber = '$new_phone' $avatar_sql $pass_sql WHERE UserID = '$user_id'";
            if ($conn->query($sql) === TRUE) {
                $_SESSION['name'] = $fullname;
                unset($_SESSION['profile_otp']);
                $msg = "<p class='success-msg'>Profile updated successfully!</p>";
            }
        }
    }
}

$query = $conn->query("SELECT * FROM Users WHERE UserID = '$user_id'");
$user_data = $query->fetch_assoc();
?>

<?php include 'includes/header.php'; ?>

<style>
    :root {
        --bg-color: #f8fafc;
        --card-bg: #ffffff;
        --border-color: #e2e8f0;
        --error-red: #ef4444;
        --success-green: #10b981;
    }

    body { background-color: var(--bg-color); }

    .profile-card {
        max-width: 700px;
        margin: 50px auto;
        background: var(--card-bg);
        border-radius: 24px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        padding: 40px;
        font-family: 'Nunito', sans-serif;
    }

    .avatar-wrapper {
        position: relative;
        width: 130px;
        height: 130px;
        margin: 0 auto 30px;
    }
    .avatar-circle {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: var(--primary);
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 4rem;
        font-weight: 800;
        overflow: hidden;
        border: 5px solid #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
    
    .camera-btn {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: var(--secondary);
        color: white;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        border: 3px solid #fff;
        transition: 0.3s;
    }
    .camera-btn:hover { transform: scale(1.1); background: var(--primary); }

    .profile-card h2 { text-align: center; color: var(--secondary); font-weight: 800; margin-bottom: 30px; font-size: 1.8rem; }

    .section-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--secondary);
        margin: 30px 0 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-weight: 700; color: #64748b; margin-bottom: 8px; font-size: 0.9rem; }
    .form-group input {
        width: 100%; padding: 14px; border: 1px solid var(--border-color); border-radius: 12px;
        font-size: 1rem; transition: 0.3s; box-sizing: border-box; background: #f8fafc;
    }
    .form-group input:focus { outline: none; border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(79, 186, 151, 0.1); }
    .form-group input[readonly] { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }

 
    #otp_container {
        display: none; background: #f0fdfa; padding: 20px; border-radius: 15px; border: 1px dashed var(--primary); margin: 10px 0 25px;
    }

    .btn-update {
        width: 100%; padding: 16px; background: var(--primary); color: white; border: none; border-radius: 12px;
        font-weight: 800; font-size: 1.1rem; cursor: pointer; transition: 0.3s; margin-top: 20px;
    }
    .btn-update:hover { background: #3aa385; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(79, 186, 151, 0.3); }


    .danger-zone { margin-top: 50px; padding: 30px; border-radius: 20px; background: #fff1f2; border: 1px solid #fecaca; }
    .danger-zone h4 { color: #be123c; margin: 0 0 10px 0; font-weight: 800; }
    .btn-delete-init { background: none; border: 1.5px solid #be123c; color: #be123c; padding: 10px; border-radius: 10px; width: 100%; font-weight: 700; cursor: pointer; transition: 0.3s; }
    .btn-delete-init:hover { background: #be123c; color: #fff; }

    .error-msg { color: var(--error-red); text-align: center; font-weight: bold; background: #fee2e2; padding: 12px; border-radius: 10px; margin-bottom: 20px; }
    .success-msg { color: var(--success-green); text-align: center; font-weight: bold; background: #d1fae5; padding: 12px; border-radius: 10px; margin-bottom: 20px; }

    @media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } }
</style>

<div class="profile-card">
    <form method="POST" action="" enctype="multipart/form-data">
        
        <div class="avatar-wrapper">
            <div class="avatar-circle">
                <?php if(!empty($user_data['Avatar'])): ?>
                    <img src="<?php echo $user_data['Avatar']; ?>" alt="Avatar">
                <?php else: ?>
                    <?php echo strtoupper(substr($user_data['FullName'], 0, 1)); ?>
                <?php endif; ?>
            </div>
            <label for="avatar-upload" class="camera-btn">
                <i class="fa-solid fa-camera"></i>
            </label>
            <input type="file" id="avatar-upload" name="avatar" hidden accept="image/*">
        </div>

        <h2>My Profile</h2>

        <?php echo $msg; ?>

        <div class="section-title"><i class="fa-regular fa-user"></i> Account Information</div>
        
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($user_data['FullName']); ?>" required>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" id="email_field" value="<?php echo htmlspecialchars($user_data['Email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" id="phone_field" value="<?php echo htmlspecialchars($user_data['PhoneNumber']); ?>" required>
            </div>
        </div>

        <div id="otp_container">
            <p style="font-size: 0.85rem; color: var(--primary); font-weight: 800; margin-bottom: 10px;">Security Verification Required:</p>
            <div style="display: flex; gap: 10px;">
                <input type="text" name="otp_verify" placeholder="Enter 6-digit OTP" style="flex: 1; text-align: center; letter-spacing: 5px; font-weight: 800; background: #fff;">
                <button type="button" class="btn-send-otp" style="padding: 0 20px; background: var(--primary); color: #fff; border: none; border-radius: 10px; font-weight: bold; cursor: pointer;">Send OTP</button>
            </div>
        </div>

        <div class="form-group">
            <label>Citizen ID (Locked)</label>
            <input type="text" value="<?php echo htmlspecialchars($user_data['CitizenID']); ?>" readonly>
        </div>

        <div class="section-title"><i class="fa-solid fa-shield-halved"></i> Security</div>
        <div class="form-grid">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="Leave blank to keep current">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm your new password">
            </div>
        </div>

        <button type="submit" name="update_profile" class="btn-update">Save All Changes</button>
    </form>

    <div class="danger-zone">
        <h4>Danger Zone</h4>
        <p style="font-size: 0.85rem; color: #9f1239; margin-bottom: 15px;">Once you delete your account, there is no going back. Please be certain.</p>
        
        <form method="POST" onsubmit="return confirm('Permanently delete account?');">
            <div id="delete_otp_box" style="display: none; margin-bottom: 15px;">
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="delete_otp" placeholder="OTP for deletion" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #fecaca;">
                    <button type="button" class="btn-send-otp" style="background: #be123c; color: #fff; border: none; padding: 0 15px; border-radius: 8px; cursor: pointer;">Send OTP</button>
                </div>
            </div>
            <button type="button" id="init_delete" class="btn-delete-init">Delete My Account</button>
            <button type="submit" name="delete_account" id="final_delete" style="display: none; background: #be123c; color: #fff; border: none; padding: 12px; border-radius: 10px; width: 100%; font-weight: 800; cursor: pointer; margin-top: 10px;">Confirm Permanent Deletion</button>
        </form>
    </div>
</div>

<script>
    const initialEmail = "<?php echo $user_data['Email']; ?>";
    const initialPhone = "<?php echo $user_data['PhoneNumber']; ?>";
    const otpContainer = document.getElementById('otp_container');

    function checkChanges() {
        if (document.getElementById('email_field').value !== initialEmail || 
            document.getElementById('phone_field').value !== initialPhone) {
            otpContainer.style.display = 'block';
        } else {
            otpContainer.style.display = 'none';
        }
    }
    document.getElementById('email_field').addEventListener('input', checkChanges);
    document.getElementById('phone_field').addEventListener('input', checkChanges);

    // Gửi OTP AJAX
    document.querySelectorAll('.btn-send-otp').forEach(btn => {
        btn.addEventListener('click', function() {
            btn.innerText = 'Sending...';
            const formData = new FormData();
            formData.append('action', 'send_otp');
            fetch('profile.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(data => {
                const res = data.split('|');
                if(res[0] === 'success') {
                    alert('DEVELOPER TEST - YOUR OTP IS: ' + res[1]);
                    btn.innerText = 'Sent ✓';
                    btn.style.background = '#10b981';
                }
            });
        });
    });

    document.getElementById('init_delete').addEventListener('click', function() {
        this.style.display = 'none';
        document.getElementById('delete_otp_box').style.display = 'block';
        document.getElementById('final_delete').style.display = 'block';
    });

    // Preview ảnh
    document.getElementById('avatar-upload').addEventListener('change', function(e) {
        if (e.target.files[0]) {
            alert('Avatar selected! Don\'t forget to click "Save All Changes".');
        }
    });
</script>

<?php include 'includes/footer.php'; ?>