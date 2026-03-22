<?php
// send_sms_alert.php
// Cronjob: Send SMS alerts to customers 10-15 minutes before pickup

require_once __DIR__ . '/../config/db.php';

require_once __DIR__ . '/../includes/functions.php';

// Example user and pickup for test case
$targetCustomerId = 1016;  // set the specific user id
$targetPickup = new DateTime('2026-03-22 16:04:57', new DateTimeZone('Asia/Ho_Chi_Minh'));

// Use exact pickup time for demo (no +/- window)
$sql = "SELECT b.BookingID, b.PickupDateTime, u.PhoneNumber, v.RegNumber, v.MakeModel, v.Color
        FROM bookings b
        JOIN users u ON b.CustomerID = u.UserID
        JOIN vehicles v ON b.VehicleID = v.VehicleID
        WHERE b.Status = 'Confirmed'
          AND b.CustomerID = ?
          AND b.PickupDateTime = ?";

$targetPickupStr = $targetPickup->format('Y-m-d H:i:s');
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $targetCustomerId, $targetPickupStr);
$stmt->execute();
$result = $stmt->get_result();


while ($row = $result->fetch_assoc()) {
    $message = "Your car is arriving soon! Registration: {$row['RegNumber']}, Color: {$row['Color']}, Make: {$row['MakeModel']}.";
    sendSMS($row['PhoneNumber'], $message);
    echo "[INFO] Sent alert 15 minutes before pickup at {$row['PickupDateTime']} for customer {$row['PhoneNumber']}\n";
}

$stmt->close();
$conn->close();
?>
