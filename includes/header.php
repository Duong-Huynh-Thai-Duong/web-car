<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HireMyCar - Professional Car Rental</title>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4FBA97; 
            --secondary: #2c3e50;
            --text-dark: #333;
            --white: #ffffff;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        body { font-family: 'Nunito', sans-serif; margin: 0; }
        
        header {
            background: var(--white);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 15px 0; 
        }
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative; 
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        .logo-img {
            height: 50px; 
            margin-right: 15px;
        }
        .logo-text {
            font-size: 1.8rem; 
            font-weight: 800;
            color: var(--secondary);
            letter-spacing: -0.5px;
        }
        .logo-text span {
            color: var(--primary);
        }
        
        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        .nav-links a {
            text-decoration: none;
            color: var(--secondary);
            font-weight: 700;
            font-size: 1.05rem;
            padding: 8px 0;
            position: relative;
            transition: color 0.3s;
        }
        
        .nav-links > a:not(.btn-nav)::after,
        .dropdown > a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background-color: var(--primary);
            transition: width 0.3s ease-in-out;
        }
        .nav-links > a:not(.btn-nav):hover,
        .dropdown:hover > a {
            color: var(--primary);
        }
        .nav-links > a:not(.btn-nav):hover::after,
        .dropdown:hover > a::after {
            width: 100%;
        }

        .dropdown {
            position: relative;
            display: inline-block;
            padding-bottom: 25px; 
            margin-bottom: -25px;
        }
        .dropdown-content {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            background-color: var(--white);
            min-width: 200px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-radius: 8px;
            z-index: 1000;
            top: 100%;
            left: 0;
            overflow: hidden;
            border: 1px solid #eaeaea;
            transform: translateY(-10px);
            transition: visibility 0s linear 0.3s, opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        }
        .dropdown-content a {
            color: var(--secondary);
            padding: 12px 20px !important;
            display: block;
        }
        .dropdown-content a::after { display: none !important; }
        .dropdown-content a:hover {
            background-color: #f4fdf9;
            color: var(--primary);
        }
        .dropdown:hover .dropdown-content {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0s; 
        }

        /* Buttons */
        .btn-nav {
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 800 !important;
            background-color: var(--primary);
            color: var(--white) !important;
            box-shadow: 0 4px 10px rgba(79, 186, 151, 0.3);
            transition: all 0.3s ease;
            border: 2px solid transparent; 
            white-space: nowrap; 
        }
        .btn-nav:hover {
            background-color: var(--white);
            color: var(--primary) !important;
            border-color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(79, 186, 151, 0.4);
        }
        .btn-logout { background-color: #e74c3c !important; box-shadow: 0 4px 10px rgba(231, 76, 60, 0.3) !important; }
        .btn-logout:hover { color: #e74c3c !important; border-color: #e74c3c !important; }

        .user-welcome {
            font-weight: 700;
            color: var(--secondary);
            font-size: 1.1rem;
            margin-left: 10px;
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            text-decoration: none !important;
            border-left: 2px solid #eee; 
            padding-left: 20px;
            transition: transform 0.3s ease;
        }
        .user-welcome span { font-size: 0.85rem; color: #888; font-weight: 600; }
        .user-welcome .user-name { color: var(--primary); }
        .user-welcome::after { display: none !important; }

        .mobile-menu-btn {
            display: none;
            cursor: pointer;
            flex-direction: column;
            gap: 6px;
            z-index: 1001; 
        }
        .mobile-menu-btn .bar {
            width: 28px;
            height: 3px;
            background-color: var(--secondary);
            border-radius: 3px;
            transition: all 0.3s ease-in-out;
        }

        @media (max-width: 1050px) {
            .mobile-menu-btn {
                display: flex; 
            }

            .nav-links {
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: var(--white);
                flex-direction: column;
                align-items: center;
                padding: 0;
                box-shadow: 0 10px 15px rgba(0,0,0,0.1);
                border-top: 1px solid #eee;
                
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.4s ease-in-out, padding 0.4s ease-in-out;
            }

            .nav-links.active {
                max-height: 500px; 
                padding: 20px 0;
                gap: 15px;
            }

            .user-welcome {
                border-left: none;
                padding-left: 0;
                margin-left: 0;
                align-items: center;
                text-align: center;
                margin-bottom: 10px;
            }
            .dropdown { padding-bottom: 0; margin-bottom: 0; text-align: center; }
            
            .dropdown-content {
                position: static;
                box-shadow: none;
                border: none;
                background: #f9f9f9;
                display: none;
                visibility: visible;
                opacity: 1;
                transform: none;
                margin-top: 10px;
            }
            .dropdown:hover .dropdown-content {
                display: block; 
            }

            .mobile-menu-btn.active .bar:nth-child(1) { transform: translateY(9px) rotate(45deg); }
            .mobile-menu-btn.active .bar:nth-child(2) { opacity: 0; }
            .mobile-menu-btn.active .bar:nth-child(3) { transform: translateY(-9px) rotate(-45deg); }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo-container">
                <img src="assets/images/logo-bunny.png" alt="HireMyCar Logo" class="logo-img">
                <span class="logo-text">Hire<span>MyCar</span></span>
            </a>
            
            <div class="mobile-menu-btn" id="mobile-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
            
            <div class="nav-links" id="nav-links">
                <a href="index.php">Home</a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <a href="#" class="dropbtn">Services ▾</a>
                        <div class="dropdown-content">
                            <a href="booking.php">Car Booking</a>
                            <a href="consignment.php">Car Consignment</a>
                        </div>
                    </div>
                    
                    <a href="history.php">My Bookings</a>
                    
                    <a href="profile.php" class="user-welcome">
                        <span>Welcome back,</span>
                        <div class="user-name"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
                    </a>
                    
                    <a href="logout.php" class="btn-nav btn-logout">Log out</a>

                <?php else: ?>
                    <a href="consignment.php" class="nav-link">Car Consignment</a> 
                    <a href="login.php">Log in</a>
                    <a href="register.php" class="btn-nav">Register Now</a>
                <?php endif; ?>

            </div>
        </nav>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu');
            const navLinks = document.getElementById('nav-links');

            mobileMenuBtn.addEventListener('click', function() {
                mobileMenuBtn.classList.toggle('active');
                navLinks.classList.toggle('active');
            });
        });
    </script>