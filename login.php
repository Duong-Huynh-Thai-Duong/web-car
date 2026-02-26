<?php
session_start();
include 'config/db.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Users WHERE Email = '$email' AND PasswordHash = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $_SESSION['user_id'] = $row['UserID'];
        $_SESSION['name'] = $row['FullName'];
        $_SESSION['role'] = $row['Role'];

        if ($row['Role'] == 'Admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $msg = "<p style='color: red; text-align: center; font-weight: bold;'>Invalid Email or Password!</p>";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div style="max-width: 500px; margin: 80px auto; padding: 40px 30px; background: var(--white); border-radius: 16px; box-shadow: var(--shadow);">
    <h2 style="text-align: center; color: var(--secondary); margin-bottom: 25px; font-weight: 800;">Welcome</h2>
    
    <?php echo $msg; ?>
    
    <form method="POST" action="">
        <div style="margin-bottom: 20px;">
            <label style="font-weight: 600; color: var(--secondary); display: block; margin-bottom: 8px;">Email Address</label>
            <input type="email" name="email" required style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 1rem; background: #f9f9f9;">
        </div>
        
        <div style="margin-bottom: 30px;">
            <label style="font-weight: 600; color: var(--secondary); display: block; margin-bottom: 8px;">Password</label>
            <input type="password" name="password" required style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 1rem; background: #f9f9f9;">
        </div>
        
        <button type="submit" name="login" style="width: 100%; padding: 15px; background: var(--primary); color: var(--white); border: none; border-radius: 8px; font-weight: bold; font-size: 1.1rem; cursor: pointer; transition: 0.3s;">Log In</button>
    </form>
    
    <p style="text-align: center; margin-top: 25px; color: #666;">
        Don't have an account? <a href="register.php" style="color: var(--primary); font-weight: bold; text-decoration: none;">Register here</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>