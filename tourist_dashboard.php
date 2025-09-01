<?php
session_start();

// ✅ Redirect if not logged in as tourist
if (!isset($_SESSION['tourist_id'])) {
    header("Location: login.php");
    exit();
}

$first_name = $_SESSION['first_name'];
$last_name  = $_SESSION['last_name'];
$email      = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tourist Dashboard</title>
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
      padding: 30px;
    }
    .card {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 4px 6px rgba(0,0,0,0.1);
      max-width: 600px;
      margin: 0 auto;
      text-align: center;
    }
    .card h2 {
      margin: 10px 0;
      color: #8B4513;
    }
    .card p {
      margin: 5px 0;
      color: #555;
    }
    .btn {
      display: inline-block;
      margin-top: 15px;
      padding: 10px 20px;
      background: #8B4513;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
    }
    .btn:hover {
      background: #5C3317;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <div class="navbar">
    <h1>Tourist Dashboard</h1>
    <div>
      <a href="tourist_dashboard.php">Home</a>
      <a href="browse_resorts.php">Browse Resorts</a>
      <a href="my_bookings.php">My Bookings</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container">
    <div class="card">
      <h2>Welcome, <?= htmlspecialchars($first_name . " " . $last_name) ?> 👋</h2>
      <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
      <p>You are now logged in as a tourist. Start exploring resorts and book your next adventure!</p>
      <a href="browse_resorts.php" class="btn">Explore Resorts</a>
    </div>
  </div>
</body>
</html>
