<?php
session_start();

// ✅ Redirect if not logged in as tourist
if (!isset($_SESSION['tourist_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Database connection

$first_name = $_SESSION['first_name'];
$last_name  = $_SESSION['last_name'];
$email      = $_SESSION['email'];

// ✅ Fetch bookings joined with resort + accommodation
$sql = "
    SELECT 
        b.booking_id,
        b.reference_no,
        b.check_in,
        b.check_out,
        b.amount,
        b.status,
        b.created_at,
        r.resort_name,
        r.resort_picture,
        a.name   AS accommodation_name,
        a.picture AS accommodation_picture
    FROM bookings b
    LEFT JOIN resorts r ON b.resort_id = r.resort_id
    LEFT JOIN accommodations a ON b.accommodation_id = a.accommodation_id
    WHERE b.customer_email = ?
    ORDER BY b.created_at DESC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    // Helpful error so you can see what's wrong if the SQL changes
    die("SQL prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $email);
if (!$stmt->execute()) {
    die("SQL execute failed: " . $stmt->error);
}
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Bookings</title>
  <link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Delius', cursive;
      background: #f5f5f5;
    }
    /* ✅ Same brown navbar as your other pages */
    .navbar {
      background: #8B4513;
      color: white;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .navbar h1 {
      margin: 0;
      font-size: 22px;
    }
    .navbar a {
      color: white;
      text-decoration: none;
      margin-left: 15px;
      font-weight: bold;
    }

    .container {
      padding: 20px;
      max-width: 1000px;
      margin: auto;
    }
    h2 {
      color: #8B4513;
      margin-top: 10px;
    }
    .booking-card {
      display: flex;
      background: white;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-bottom: 20px;
      overflow: hidden;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .booking-card img {
      width: 200px;
      height: 140px;
      object-fit: cover;
      background: #eee;
    }
    .booking-details {
      padding: 15px;
      flex: 1;
    }
    .booking-details h3 {
      margin: 0 0 6px;
      color: #8B4513;
    }
    .booking-details p {
      margin: 4px 0;
      font-size: 14px;
      color: #333;
    }
    .muted { color: #666; font-size: 12px; }
    .status {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 5px;
      font-size: 12px;
      font-weight: bold;
      margin-top: 6px;
    }
    .status.pending   { background: #ffeb99; color: #8B4513; }
    .status.confirmed { background: #c8f7c5; color: #2d7a2d; }
    .status.cancelled { background: #f7c5c5; color: #8b0000; }
    .empty {
      background: #fff;
      border: 1px dashed #ccc;
      border-radius: 8px;
      padding: 20px;
    }
    .empty a { color: #8B4513; font-weight: bold; }
  </style>
</head>
<body>
  <!-- ✅ Navbar -->
  <div class="navbar">
    <h1>My Bookings</h1>
    <div>
      <a href="tourist_dashboard.php">Home</a>
      <a href="browse_resorts.php">Browse Resorts</a>
      <a href="my_bookings.php">My Bookings</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <!-- ✅ Booking List -->
  <div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($first_name); ?>! Here are your bookings:</h2>

    <?php if (!empty($bookings)): ?>
      <?php foreach ($bookings as $b): 
        $img = $b['accommodation_picture'] ?: $b['resort_picture'] ?: 'default_resort.png';
      ?>
        <div class="booking-card">
          <img src="<?php echo htmlspecialchars($img); ?>" 
               alt="<?php echo htmlspecialchars($b['resort_name'] ?? 'Resort'); ?>">
          <div class="booking-details">
            <h3><?php echo htmlspecialchars($b['resort_name'] ?? 'Resort'); ?></h3>
            <p><strong>Reference:</strong> <?php echo htmlspecialchars($b['reference_no']); ?></p>
            <p><strong>Accommodation:</strong> <?php echo htmlspecialchars($b['accommodation_name'] ?? 'N/A'); ?></p>
            <p><strong>Check-in:</strong> <?php echo htmlspecialchars($b['check_in']); ?></p>
            <p><strong>Check-out:</strong> <?php echo htmlspecialchars($b['check_out']); ?></p>
            <p><strong>Amount:</strong> ₱<?php echo number_format((float)$b['amount'], 2); ?></p>
            <span class="status <?php echo strtolower($b['status']); ?>">
              <?php echo ucfirst($b['status']); ?>
            </span>
            <div class="muted">Booked on <?php echo htmlspecialchars(date('M d, Y H:i', strtotime($b['created_at']))); ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty">
        You have no bookings yet. <a href="browse_resorts.php">Browse resorts</a> to make one!
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
