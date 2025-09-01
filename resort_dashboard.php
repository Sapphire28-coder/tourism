<?php
session_start();
include 'db.php';

// Make sure user is logged in as resort
if (!isset($_SESSION['resort_id'])) {
    header("Location: login.php");
    exit();
}

$resort_id = $_SESSION['resort_id'];

// Fetch resort details
$sql = "SELECT * FROM resorts WHERE resort_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resort_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("Resort not found.");
}

$resort = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Resort Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Delius', cursive;
      background: #f5f5f5;
    }

    /* Main content area */
    .main {
      margin-left: 250px; /* keep content right of sidebar */
      padding: 20px;
    }

    /* Profile card */
    .profile {
      display: flex;
      align-items: center;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    .profile img {
      width: 100px;
      height: 100px;
      border-radius: 12px;
      object-fit: cover;
      margin-right: 20px;
      border: 2px solid #8B4513;
    }
    .profile h3 {
      margin: 0;
      font-size: 22px;
      color: #8B4513;
    }
    .profile p {
      margin: 4px 0;
      font-size: 14px;
      color: #444;
    }

    /* Dashboard cards */
    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
    }
    .dashboard-cards .card {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      transition: transform 0.2s ease;
    }
    .dashboard-cards .card:hover {
      transform: translateY(-5px);
    }
    .dashboard-cards .card h3 {
      margin: 0;
      font-size: 18px;
      color: #333;
    }
    .dashboard-cards .card h2 {
      font-size: 32px;
      margin: 10px 0;
      color: #8B4513;
    }
    .dashboard-cards .card p {
      font-size: 14px;
      color: #666;
      margin: 0;
    }
  </style>
</head>
<body>
  
  <!-- Sidebar (unchanged) -->
  <?php include 'resort_sidebar.php'; ?>

  <!-- Main content -->
  <div class="main">
    <!-- Resort profile header -->
    <div class="profile">
      <?php if (!empty($resort['logo'])): ?>
        <img src="uploads/<?= htmlspecialchars($resort['logo']) ?>" alt="Resort Logo">
      <?php else: ?>
        <img src="uploads/default_logo.png" alt="Default Logo">
      <?php endif; ?>
      <div>
        <h3><?= htmlspecialchars($resort['resort_name']) ?></h3>
        <p><strong>Owner:</strong> <?= htmlspecialchars($resort['owner_name']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($resort['status']) ?></p>
      </div>
    </div>

    <!-- 4 Dashboard Cards -->
    <div class="dashboard-cards">
      <div class="card">
        <h3>Total Bookings</h3>
        <h2>120</h2>
        <p>Overall reservations</p>
      </div>
      <div class="card">
        <h3>Active Guests</h3>
        <h2>35</h2>
        <p>Currently staying</p>
      </div>
      <div class="card">
        <h3>Available Rooms</h3>
        <h2>15</h2>
        <p>Ready for booking</p>
      </div>
      <div class="card">
        <h3>Revenue</h3>
        <h2>₱250k</h2>
        <p>This month</p>
      </div>
    </div>
  </div>

</body>
</html>
