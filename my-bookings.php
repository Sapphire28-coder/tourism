<?php
session_start();

// ✅ Redirect if not logged in as tourist
if (!isset($_SESSION['tourist_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Database connection

$tourist_id = $_SESSION['tourist_id'];
$first_name = $_SESSION['first_name'];
$last_name  = $_SESSION['last_name'];
$email      = $_SESSION['email'];

// ✅ Fetch bookings joined with resort + accommodation
$sql = "
    SELECT b.booking_id, b.check_in, b.check_out, b.amount, b.status,
           r.resort_name, r.resort_picture,
           a.name AS accommodation_name, a.picture AS accommodation_picture
    FROM bookings b
    JOIN resorts r ON b.resort_id = r.resort_id
    JOIN accommodations a ON b.accommodation_id = a.accommodation_id
    WHERE b.tourist_id = ?
    ORDER BY b.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tourist_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
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
      object-fit: cover;
    }
    .booking-details {
      padding: 15px;
      flex: 1;
    }
    .booking-details h3 {
      margin: 0;
      color: #8B4513;
    }
    .booking-details p {
      margin: 5px 0;
      font-size: 14px;
    }
    .status {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 5px;
      font-size: 13px;
      font-weight: bold;
    }
    .status.pending { background: #ffeb99; color: #8B4513; }
    .status.confirmed { background: #c8f7c5; color: #2d7a2d; }
    .status.cancelled { background: #f7c5c5; color: #8b0000; }
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

    <?php if (count($bookings) > 0): ?>
      <?php foreach ($bookings as $booking): ?>
        <div class="booking-card">
          <img src="uploads/<?php echo $booking['accommodation_picture'] ?: $booking['resort_picture']; ?>" 
               alt="<?php echo htmlspecialchars($booking['resort_name']); ?>">
          <div class="booking-details">
            <h3><?php echo htmlspecialchars($booking['resort_name']); ?></h3>
            <p><strong>Accommodation:</strong> <?php echo htmlspecialchars($booking['accommodation_name']); ?></p>
            <p><strong>Check-in:</strong> <?php echo htmlspecialchars($booking['check_in']); ?></p>
            <p><strong>Check-out:</strong> <?php echo htmlspecialchars($booking['check_out']); ?></p>
            <p><strong>Amount:</strong> ₱<?php echo number_format($booking['amount'], 2); ?></p>
            <p><span class="status <?php echo strtolower($booking['status']); ?>">
              <?php echo ucfirst($booking['status']); ?>
            </span></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>You have no bookings yet. <a href="browse_resorts.php">Browse resorts</a> to make one!</p>
    <?php endif; ?>
  </div>
</body>
</html>
