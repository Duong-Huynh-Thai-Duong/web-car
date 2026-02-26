<?php
session_start();
include 'config/db.php';

$reviews = [];
$review_sql = "SELECT Users.FullName, Reviews.Comment, Reviews.Rating 
               FROM Reviews 
               JOIN Users ON Reviews.CustomerID = Users.UserID 
               WHERE Reviews.Rating = 5 
               ORDER BY Reviews.ReviewID DESC LIMIT 10";

$result = $conn->query($review_sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
} else {
    $reviews = [
        ['FullName' => 'Michael T.', 'Comment' => 'Absolutely brilliant service! The booking process was seamless, and the car was in pristine condition. Highly recommend for any trips.', 'Rating' => 5],
        ['FullName' => 'Sarah J.', 'Comment' => 'Customer support was very helpful when I needed to change my pickup time. The VIP luxury option made my business trip so much more comfortable.', 'Rating' => 5],
        ['FullName' => 'David W.', 'Comment' => 'Great pricing and transparent fees. No hidden charges at drop-off. Will definitely be using HireMyCar again!', 'Rating' => 5],
        ['FullName' => 'Emily R.', 'Comment' => 'The car was delivered to my hotel exactly on time. Very professional service and clean vehicles.', 'Rating' => 5],
        ['FullName' => 'James C.', 'Comment' => 'First time renting a car online and this platform made it incredibly easy. The 7-seater SUV was perfect for our family road trip.', 'Rating' => 5]
    ];
}
?>

<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .hero-section { position: relative; width: 100%; height: 60vh; min-height: 450px; display: flex; align-items: center; justify-content: center; text-align: center; color: var(--white); margin-bottom: 20px; }
    .hero-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -2; }
    .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.7)); z-index: -1; }
    .hero-content { position: relative; z-index: 1; padding: 0 20px; max-width: 800px; }
    .hero-content h1 { font-size: 3.5rem; font-weight: 900; margin-bottom: 15px; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); }
    .hero-content p { font-size: 1.25rem; font-weight: 600; margin-bottom: 35px; text-shadow: 1px 1px 4px rgba(0,0,0,0.5); }
    .btn-hero { padding: 16px 45px; background: var(--primary); color: var(--white); font-size: 1.15rem; font-weight: 800; text-decoration: none; border-radius: 8px; transition: 0.3s; box-shadow: 0 5px 20px rgba(79, 186, 151, 0.4); display: inline-block; }
    .btn-hero:hover { background: var(--white); color: var(--primary); transform: translateY(-3px); }
    
    .home-wrapper { max-width: 1200px; margin: 40px auto 80px; padding: 0 20px; font-family: 'Nunito', sans-serif; overflow: hidden; }
    .section-title { text-align: center; color: var(--secondary); font-size: 1.8rem; font-weight: 800; margin-bottom: 30px; margin-top: 60px; }
    
    .brands-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; }
    .brand-card { width: 130px; height: 110px; background: var(--white); border: 1px solid #eaeaea; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-decoration: none; color: var(--secondary); transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .brand-card:hover { border-color: var(--primary); box-shadow: 0 5px 15px rgba(79, 186, 151, 0.15); transform: translateY(-3px); }
    .brand-card img { height: 45px; object-fit: contain; margin-bottom: 10px; pointer-events: none; }
    .brand-card span { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; }
    
    .carousel-container { position: relative; width: 100%; padding-bottom: 20px; }
    .drag-track { 
        display: flex; overflow-x: auto; 
        scroll-snap-type: x mandatory; scroll-behavior: smooth; 
        scrollbar-width: none; -ms-overflow-style: none; 
        padding: 10px 5px; cursor: grab;
    }
    .drag-track::-webkit-scrollbar { display: none; }
    .drag-track.active-drag { cursor: grabbing; scroll-snap-type: none; scroll-behavior: auto; }


    .carousel-dots { display: flex; justify-content: center; gap: 8px; margin-top: 15px; }
    .dot { width: 10px; height: 10px; border-radius: 50%; background: #ddd; cursor: pointer; transition: 0.4s ease; }
    .dot.active { background: var(--primary); width: 28px; border-radius: 5px; }

    .locations-track { gap: 30px; }
    .loc-card { 
        flex: 0 0 calc(33.333% - 20px); 
        scroll-snap-align: start; user-select: none;
        background: var(--white); border-radius: 16px; overflow: hidden; 
        border: 1px solid #eaeaea; box-shadow: 0 4px 15px rgba(0,0,0,0.04); transition: transform 0.3s ease; 
    }
    .loc-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
    .loc-img { width: 100%; height: 180px; object-fit: cover; pointer-events: none; }
    .loc-content { padding: 20px; display: flex; justify-content: space-between; align-items: center; }
    .loc-info h4 { margin: 0 0 5px 0; color: var(--secondary); font-size: 1.25rem; font-weight: 800; }
    .loc-info p { margin: 0; color: var(--primary); font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 6px; }
    .btn-outline-primary { padding: 8px 18px; border: 2px solid var(--primary); color: var(--primary); background: transparent; border-radius: 6px; font-weight: 700; text-decoration: none; transition: 0.3s; white-space: nowrap; }
    .btn-outline-primary:hover { background: var(--primary); color: var(--white); }

    /* --- REVIEW CAROUSEL --- */
    .reviews-track { gap: 25px; }
    .review-slide {
        flex: 0 0 calc(33.333% - 17px); 
        scroll-snap-align: start; user-select: none;
        background: #ffffff; padding: 30px; border-radius: 16px; 
        border: 1px solid #f0f0f0; box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        display: flex; flex-direction: column; justify-content: space-between;
    }
    .review-slide .stars { color: #f39c12; margin-bottom: 15px; font-size: 1.1rem; }
    .review-slide .review-text { color: #555; font-size: 1rem; line-height: 1.6; font-style: italic; margin-bottom: 25px; }
    .review-slide .review-author { display: flex; align-items: center; gap: 15px; border-top: 1px solid #eee; padding-top: 20px; }
    .avatar-placeholder { width: 50px; height: 50px; border-radius: 50%; background: #e9ecef; color: #888; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .author-info h5 { margin: 0; color: var(--secondary); font-size: 1.05rem; font-weight: 800; }
    .author-info span { font-size: 0.85rem; color: #888; }

    /* --- EXPLORE BANNER --- */
    .explore-banner { display: flex; align-items: center; margin-top: 80px; gap: 40px; }
    .explore-img-pattern { flex: 1.5; border-radius: 16px; overflow: hidden; }
    .explore-img-pattern img { width: 100%; height: auto; display: block; pointer-events: none; }
    .explore-text { flex: 1; padding-right: 20px; }
    .explore-text h2 { color: var(--primary); font-size: 2.2rem; font-weight: 800; margin-top: 0; margin-bottom: 10px; line-height: 1.2; }
    .explore-text p { color: var(--secondary); font-size: 1.2rem; font-weight: 600; margin-bottom: 25px; }
    .btn-solid-primary { padding: 12px 35px; background: var(--primary); color: var(--white); border: none; border-radius: 6px; font-weight: 800; font-size: 1.1rem; text-decoration: none; display: inline-block; transition: 0.3s; box-shadow: 0 4px 15px rgba(79, 186, 151, 0.4); }
    .btn-solid-primary:hover { background: #3aa385; transform: translateY(-2px); }

    @media (max-width: 992px) { 
        .loc-card { flex: 0 0 calc(50% - 15px); }
        .review-slide { flex: 0 0 calc(50% - 12.5px); }
        .hero-content h1 { font-size: 2.5rem; } 
        .explore-banner { flex-direction: column-reverse; text-align: center; } 
        .explore-text { padding-right: 0; } 
    }
    @media (max-width: 768px) { 
        .loc-card { flex: 0 0 100%; }
        .review-slide { flex: 0 0 100%; }
        .brand-card { width: 100px; height: 90px; } 
        .brand-card img { height: 35px; } 
    }
</style>

<div class="hero-section">
    <img src="assets/images/home-banner.jpg" alt="HireMyCar Premium Service" class="hero-bg" onerror="this.src='https://images.unsplash.com/photo-1485291571150-772bcfc10da5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Find Your Perfect Ride</h1>
        <p>Premium car rental for your everyday needs and special journeys. Simple booking, instant confirmation.</p>
        <a href="booking.php" class="btn-hero">Book a Car Now</a>
    </div>
</div>

<div class="home-wrapper">

    <h2 class="section-title">Browse by Brand</h2>
    <div class="brands-grid">
        <a href="booking.php" class="brand-card">
            <img src="assets/images/logos/vinfast.jpg" alt="Vinfast">
            <span>Vinfast</span>
        </a>
        <a href="booking.php" class="brand-card">
            <img src="assets/images/logos/mitsubishi.jpg" alt="Mitsubishi">
            <span>Mitsubishi</span>
        </a>
        <a href="booking.php" class="brand-card">
            <img src="assets/images/logos/toyota.jpg" alt="Toyota">
            <span>Toyota</span>
        </a>
        <a href="booking.php" class="brand-card">
            <img src="assets/images/logos/hyundai.jpg" alt="Hyundai">
            <span>Hyundai</span>
        </a>
        <a href="booking.php" class="brand-card">
            <img src="assets/images/logos/kia.jpg" alt="Kia">
            <span>Kia</span>
        </a>
        <a href="booking.php" class="brand-card">
            <img src="assets/images/logos/mg.jpg" alt="MG">
            <span>MG</span>
        </a>
        <a href="booking.php" class="brand-card">
            <img src="assets/images/logos/mazda.jpg" alt="Mazda">
            <span>Mazda</span>
        </a>
    </div>

    <h2 class="section-title">Popular Destinations</h2>
    <div class="carousel-container">
        <div class="drag-track locations-track" id="locationsTrack">
            
            <div class="loc-card">
                <img src="assets/images/locations/hcm.jpg" alt="Ho Chi Minh City" class="loc-img" onerror="this.src='https://images.unsplash.com/photo-1583417319070-4a69db38a482?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                <div class="loc-content">
                    <div class="loc-info">
                        <h4>Ho Chi Minh City</h4>
                        <p><i class="fa-solid fa-car-side"></i> 500+ cars</p>
                    </div>
                    <a href="booking.php" class="btn-outline-primary">FIND A CAR</a>
                </div>
            </div>
            
            <div class="loc-card">
                <img src="assets/images/locations/hanoi.jpg" alt="Hanoi" class="loc-img" onerror="this.src='https://images.unsplash.com/photo-1528127263485-d9276b89900c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                <div class="loc-content">
                    <div class="loc-info">
                        <h4>Hanoi</h4>
                        <p><i class="fa-solid fa-car-side"></i> 300+ cars</p>
                    </div>
                    <a href="booking.php" class="btn-outline-primary">FIND A CAR</a>
                </div>
            </div>

            <div class="loc-card">
                <img src="assets/images/locations/danang.jpg" alt="Da Nang" class="loc-img" onerror="this.src='https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                <div class="loc-content">
                    <div class="loc-info">
                        <h4>Da Nang</h4>
                        <p><i class="fa-solid fa-car-side"></i> 150+ cars</p>
                    </div>
                    <a href="booking.php" class="btn-outline-primary">FIND A CAR</a>
                </div>
            </div>

            <div class="loc-card">
                <img src="assets/images/locations/dalat.jpg" alt="Da Lat" class="loc-img" onerror="this.src='https://images.unsplash.com/photo-1506461883276-594a12b11dc3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                <div class="loc-content">
                    <div class="loc-info">
                        <h4>Da Lat</h4>
                        <p><i class="fa-solid fa-car-side"></i> 100+ cars</p>
                    </div>
                    <a href="booking.php" class="btn-outline-primary">FIND A CAR</a>
                </div>
            </div>

            <div class="loc-card">
                <img src="assets/images/locations/binhduong.jpg" alt="Binh Duong" class="loc-img" onerror="this.src='https://images.unsplash.com/photo-1542361345-89e58247f2d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                <div class="loc-content">
                    <div class="loc-info">
                        <h4>Binh Duong</h4>
                        <p><i class="fa-solid fa-car-side"></i> 80+ cars</p>
                    </div>
                    <a href="booking.php" class="btn-outline-primary">FIND A CAR</a>
                </div>
            </div>

        </div>
        <div class="carousel-dots" id="locationDots"></div>
    </div>

    <h2 class="section-title">Customer Reviews</h2>
    <div class="carousel-container">
        <div class="drag-track reviews-track" id="reviewsTrack">
            <?php foreach($reviews as $review): ?>
                <div class="review-slide">
                    <div>
                        <div class="stars">
                            <?php for ($i=0; $i<$review['Rating']; $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
                        </div>
                        <p class="review-text">"<?php echo htmlspecialchars($review['Comment']); ?>"</p>
                    </div>
                    <div class="review-author">
                        <div class="avatar-placeholder"><i class="fa-solid fa-user"></i></div>
                        <div class="author-info">
                            <h5><?php echo htmlspecialchars($review['FullName']); ?></h5>
                            <span>Verified Customer</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="carousel-dots" id="reviewDots"></div>
    </div>

    <div class="explore-banner">
        <div class="explore-img-pattern">
            <img src="assets/images/pattern-cars.jpg" alt="1000+ Cars Pattern" onerror="this.src='https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80'">
        </div>
        <div class="explore-text">
            <h2>1000+ Cars & Beyond</h2>
            <p>Experience it today!</p>
            <a href="booking.php" class="btn-solid-primary">FIND A CAR</a>
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        function makeTrackDraggable(trackId) {
            const track = document.getElementById(trackId);
            if (!track) return;
            
            let isDown = false;
            let startX;
            let scrollLeft;

            track.addEventListener('mousedown', (e) => {
                isDown = true;
                track.classList.add('active-drag'); 
                startX = e.pageX - track.offsetLeft;
                scrollLeft = track.scrollLeft;
            });
            track.addEventListener('mouseleave', () => {
                isDown = false;
                track.classList.remove('active-drag');
            });
            track.addEventListener('mouseup', () => {
                isDown = false;
                track.classList.remove('active-drag');
            });
            track.addEventListener('mousemove', (e) => {
                if (!isDown) return; 
                e.preventDefault(); 
                const x = e.pageX - track.offsetLeft;
                const walk = (x - startX) * 1.5; 
                track.scrollLeft = scrollLeft - walk;
            });
        }

        function setupCarouselDots(trackId, dotsId, slideClass, gap) {
            const track = document.getElementById(trackId);
            const dotsContainer = document.getElementById(dotsId);
            const slides = document.querySelectorAll('.' + slideClass);
            
            if(!track || !dotsContainer || slides.length === 0) return;

            slides.forEach((_, index) => {
                const dot = document.createElement('div');
                dot.classList.add('dot');
                if (index === 0) dot.classList.add('active');
                
                dot.addEventListener('click', () => {
                    const slideWidth = slides[0].offsetWidth + gap; 
                    track.scrollTo({ left: index * slideWidth, behavior: 'smooth' });
                });
                dotsContainer.appendChild(dot);
            });

            const dots = dotsContainer.querySelectorAll('.dot');
            track.addEventListener('scroll', () => {
                const scrollPosition = track.scrollLeft;
                const slideWidth = slides[0].offsetWidth + gap;
                const activeIndex = Math.round(scrollPosition / slideWidth);
                
                dots.forEach(dot => dot.classList.remove('active'));
                if(dots[activeIndex]) dots[activeIndex].classList.add('active');
            });
        }

        makeTrackDraggable('locationsTrack');
        makeTrackDraggable('reviewsTrack');

        setupCarouselDots('locationsTrack', 'locationDots', 'loc-card', 30);
        setupCarouselDots('reviewsTrack', 'reviewDots', 'review-slide', 25);

    });
</script>

<?php include 'includes/footer.php'; ?>