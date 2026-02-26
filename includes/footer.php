<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .footer {
        background-color: #f8f9fa;
        color: var(--secondary);
        padding: 60px 20px 30px;
        font-family: 'Nunito', sans-serif;
        border-top: 1px solid #eaeaea;
        margin-top: 50px;
    }
    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 2fr 1.5fr 1fr 1.5fr;
        gap: 40px;
    }
    .footer-col h4 {
        color: var(--secondary);
        font-size: 1.1rem;
        font-weight: 800;
        margin-bottom: 20px;
        margin-top: 0;
    }
    .footer-col p {
        font-size: 0.9rem;
        color: #555;
        line-height: 1.6;
        margin-bottom: 10px;
    }
    .footer-col a {
        color: #555;
        text-decoration: none;
        display: block;
        font-size: 0.9rem;
        margin-bottom: 12px;
        transition: color 0.3s;
    }
    .footer-col a:hover {
        color: var(--primary);
    }
    .company-name {
        font-weight: 800;
        margin-bottom: 5px !important;
    }
    .tax-info {
        font-size: 0.85rem !important;
        color: #888 !important;
        margin-bottom: 20px !important;
    }
    
    .footer-logo {
        display: flex;
        align-items: center;
        margin-bottom: 25px; 
        text-decoration: none;
    }
    .footer-logo img {
        height: 70px; 
        margin-right: 15px; 
    }
    .footer-logo span {
        font-size: 2rem; 
        font-weight: 800;
        color: var(--secondary);
        letter-spacing: -0.5px;
    }
    .footer-logo span span {
        color: var(--primary);
    }

    .social-icons {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    .social-icons a {
        display: inline-flex;
        width: 35px;
        height: 35px;
        background: #fff;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        color: var(--secondary);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: transform 0.3s, color 0.3s;
        margin-bottom: 0;
    }
    .social-icons a:hover {
        transform: translateY(-3px);
        color: var(--primary);
    }
    .download-app {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        padding: 10px 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        width: fit-content;
    }
    .download-btn {
        background: var(--primary);
        color: #fff !important;
        padding: 5px 12px;
        border-radius: 4px;
        font-size: 0.8rem !important;
        font-weight: bold;
        margin-bottom: 0 !important;
    }
    .download-btn:hover {
        background: #3aa385;
        color: #fff !important;
    }
    .support-hotline {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--primary);
        font-weight: 800;
        font-size: 1.2rem;
        margin-top: 10px;
    }
    .copyright {
        text-align: center;
        padding-top: 30px;
        margin-top: 40px;
        border-top: 1px solid #eaeaea;
        font-size: 0.9rem;
        color: #888;
    }
    @media (max-width: 992px) {
        .footer-container {
            grid-template-columns: 1fr 1fr;
        }
    }
    @media (max-width: 576px) {
        .footer-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-col">
            <a href="index.php" class="footer-logo">
                <img src="assets/images/logo-bunny.png" alt="HireMyCar Logo">
                <span>Hire<span>MyCar</span></span>
            </a>
            <p class="company-name">HIREMYCAR MOBILITY VIETNAM LLC</p>
            <p class="tax-info">Tax Code: 0318208708. Issued: 11/12/2023</p>
            
            <p><strong>Ho Chi Minh Office</strong><br>69 B4 Street, An Loi Dong Ward, Thu Duc City, Ho Chi Minh City, Vietnam</p>
            <p><strong>Da Nang Office</strong><br>35 Thai Phien, Phuoc Ninh Ward, Hai Chau District, Da Nang City, Vietnam</p>
            <p><strong>Ha Noi Office</strong><br>Floor 4, Lancaster Luminaire Building, 1152 Lang Street, Dong Da District, Ha Noi City, Vietnam</p>
            <p>Email: support@hiremycar.com</p>
        </div>

        <div class="footer-col">
            <h4>Policies</h4>
            <a href="#">General Terms</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Platform Terms of Use</a>
            <a href="#">Car Delivery Policy</a>
            <a href="#">Payment Methods</a>

            <h4 style="margin-top: 30px;">Mobile App</h4>
            <div class="download-app">
                <i class="fa-solid fa-car-side" style="color: var(--primary); font-size: 1.5rem;"></i>
                <span style="font-weight: bold; color: var(--secondary);">Rent a car</span>
                <a href="#" class="download-btn">Download</a>
            </div>
        </div>

        <div class="footer-col">
            <h4>Service Locations</h4>
            <a href="#">Ha Noi</a>
            <a href="#">Da Nang</a>
            <a href="#">Binh Duong</a>
            <a href="#">Ho Chi Minh</a>
            <a href="#">Khanh Hoa</a>
            <a href="#">Da Lat</a>
            <a href="#">Can Tho</a>
        </div>

        <div class="footer-col">
            <h4>Social Media</h4>
            <div class="social-icons">
                <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                <a href="#"><i class="fa-brands fa-youtube"></i></a>
                <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
            </div>

            <h4 style="margin-top: 30px;">Support</h4>
            <a href="#">Service Regulations</a>
            <div class="support-hotline">
                <i class="fa-solid fa-phone-volume"></i>
                1900 5335
            </div>
        </div>
    </div>
    <div class="copyright">
        &copy; <?php echo date("Y"); ?> HireMyCar. Coursework Project. All rights reserved.
    </div>
</footer>
</body>
</html>