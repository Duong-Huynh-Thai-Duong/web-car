<?php
session_start();
include 'config/db.php';

// Prevent unauthorized access
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $booking_id = $_POST['booking_id'];
    $rating = $_POST['rating'];
    $comment = $conn->real_escape_string($_POST['comment']);

    // Check if review already exists to prevent duplicates
    $check = $conn->query("SELECT * FROM Reviews WHERE BookingID = '$booking_id'");
    if ($check->num_rows == 0) {
        $sql = "INSERT INTO Reviews (BookingID, Rating, Comment) VALUES ('$booking_id', '$rating', '$comment')";
        if ($conn->query($sql) === TRUE) {
            $msg = "<script>alert('Thank you! Your review has been posted on our homepage.'); window.location.href='booking.php';</script>";
        } else {
            $msg = "<script>alert('Error posting review.');</script>";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<?php echo $msg; ?>

<style>
    .bookings-container {
        max-width: 1000px;
        margin: 50px auto;
        padding: 0 20px;
        min-height: 60vh;
    }
    .page-title {
        color: var(--secondary);
        font-weight: 800;
        margin-bottom: 30px;
        font-size: 2rem;
        border-bottom: 3px solid var(--primary);
        display: inline-block;
        padding-bottom: 10px;
    }
    .booking-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
    }
    .booking-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: var(--shadow);
        padding: 20px;
        border-top: 5px solid var(--primary);
        transition: transform 0.3s ease;
    }
    .booking-card:hover {
        transform: translateY(-5px);
    }
    .booking-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    .booking-id {
        font-weight: 800;
        color: var(--secondary);
        font-size: 1.1rem;
    }
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: bold;
    }
    .status-completed { background: #e8f5e9; color: #2e7d32; }
    .status-pending { background: #fff8e1; color: #f57f17; }
    .status-cancelled { background: #ffebee; color: #c62828; }
    
    .booking-body p {
        margin: 8px 0;
        color: #555;
        font-size: 0.95rem;
    }
    .booking-body strong {
        color: var(--secondary);
    }
    .booking-footer {
        margin-top: 20px;
        text-align: right;
    }
    .btn-review {
        background: var(--primary);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }
    .btn-review:hover {
        background: #3aa385;
    }
    .reviewed-text {
        color: #f39c12;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 5px;
    }

    /* MODAL CSS */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6);
        z-index: 2000;
        justify-content: center;
        align-items: center;
    }
    .modal-box {
        background: white;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .modal-title { margin-top: 0; color: var(--secondary); }
    .close-modal {
        float: right; cursor: pointer; font-size: 1.5rem; color: #888; border: none; background: none;
    }
    textarea {
        width: 100%; height: 100px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; margin-bottom: 15px; font-family: inherit; resize: none;
    }
</style>

<div class="bookings-container">
    <h2 class="page-title">My Bookings</h2>

    <div class="booking-grid">
        <?php
        // Fetch bookings for logged-in user
        $sql = "SELECT * FROM Bookings WHERE CustomerID = '$user_id' ORDER BY PickupDateTime DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $status_class = "status-" . strtolower($row['Status']);
                $b_id = $row['BookingID'];
                ?>
                
                <div class="booking-card">
                    <div class="booking-header">
                        <span class="booking-id">#TRIP-<?php echo $b_id; ?></span>
                        <span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($row['Status']); ?></span>
                    </div>
                    <div class="booking-body">
                        <p><strong>📍 From:</strong> <?php echo htmlspecialchars($row['PickupLocation']); ?></p>
                        <p><strong>🚩 To:</strong> <?php echo htmlspecialchars($row['Destination']); ?></p>
                        <p><strong>📅 Date:</strong> <?php echo date('d M Y, H:i', strtotime($row['PickupDateTime'])); ?></p>
                        <p><strong>💵 Fare:</strong> $<?php echo number_format($row['TotalFare'], 2); ?></p>
                    </div>
                    <div class="booking-footer">
                        <?php
                        // Check if this booking has been reviewed
                        $check_rev = $conn->query("SELECT * FROM Reviews WHERE BookingID = '$b_id'");
                        
                        if ($check_rev->num_rows > 0) {
                            echo '<span class="reviewed-text">★★★★★ Reviewed</span>';
                        } elseif (strtolower($row['Status']) == 'completed') {
                            echo '<button onclick="openReviewModal('.$b_id.')" class="btn-review">Leave a Review</button>';
                        } else {
                            echo '<span style="color:#888; font-size: 0.9rem;">Review available after trip</span>';
                        }
                        ?>
                    </div>
                </div>

                <?php
            }
        } else {
            echo "<div style='grid-column: 1 / -1; text-align: center; padding: 50px; background: #f9f9f9; border-radius: 12px;'>";
            echo "<h3>You have no bookings yet.</h3>";
            echo "<p style='color:#666; margin-bottom: 20px;'>Start exploring the world with our premium cars.</p>";
            echo "<a href='index.php' class='btn-review' style='text-decoration:none;'>Find a Car Now</a>";
            echo "</div>";
        }
        ?>
    </div>
</div>

<div class="modal-overlay" id="reviewModal">
    <div class="modal-box">
        <button class="close-modal" onclick="closeReviewModal()">&times;</button>
        <h3 class="modal-title">Rate Your Trip</h3>
        <p style="color: #666; margin-bottom: 20px;">Share your experience to help others.</p>
        
        <form method="POST" action="">
            <input type="hidden" name="booking_id" id="modal_booking_id" value="">
            
            <label style="font-weight: bold; color: var(--secondary); display: block; margin-bottom: 10px;">Rating</label>
            <select name="rating" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 15px; font-size: 1rem;">
                <option value="5">★★★★★ - Excellent</option>
                <option value="4">★★★★☆ - Very Good</option>
                <option value="3">★★★☆☆ - Average</option>
                <option value="2">★★☆☆☆ - Poor</option>
                <option value="1">★☆☆☆☆ - Terrible</option>
            </select>
            
            <label style="font-weight: bold; color: var(--secondary); display: block; margin-bottom: 10px;">Your Comment</label>
            <textarea name="comment" required placeholder="Tell us about the car, the driver, and the journey..."></textarea>
            
            <button type="submit" name="submit_review" style="width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 1.1rem; cursor: pointer;">Post Review</button>
        </form>
    </div>
</div>

<script>
    function openReviewModal(bookingId) {
        document.getElementById('modal_booking_id').value = bookingId;
        document.getElementById('reviewModal').style.display = 'flex';
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').style.display = 'none';
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        let modal = document.getElementById('reviewModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<?php include 'includes/footer.php'; ?>