<?php
session_start();

// ✅ Only admin can access
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_email = $_SESSION['email'] ?? "Admin";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Delius', cursive;
      background: #f5f5f5;
    }
    .content {
      margin-left: 240px; /* leave space for sidebar */
      padding: 20px;
    }
    h1 {
      color: #8B4513;
    }
    .cards {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }
    .card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      flex: 1 1 200px;
      text-align: center;
    }
    .card h2 {
      margin: 0;
      font-size: 20px;
      color: #333;
    }
    .card p {
      margin-top: 10px;
      font-size: 16px;
      color: #555;
    }
  </style>
</head>
<body>

<?php include "admin_sidebar.php"; ?>

<div class="content">
  <h1>Welcome, <?= htmlspecialchars($admin_email) ?> 👋</h1>
  <p>This is your admin dashboard. Use the sidebar to manage the system.</p>

  <div class="cards">
    <div class="card">
      <h2>Resorts</h2>
      <p>Manage resort partners</p>
    </div>
    <div class="card">
      <h2>Tourists</h2>
      <p>View and manage users</p>
    </div>
    <div class="card">
      <h2>Bookings</h2>
      <p>Monitor reservations</p>
    </div>
    <div class="card">
      <h2>Admins</h2>
      <p>Manage admin accounts</p>
    </div>
  </div>
</div>

</body>
</html>
