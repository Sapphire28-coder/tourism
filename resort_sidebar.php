<!-- resort_sidebar.php -->
<link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">

<style>
  body {
    margin: 0;
    font-family: 'Delius', cursive;
    background: #fff;
    color: #333;
  }

  .sidebar {
    width: 220px;
    background: #fff;
    border-right: 2px solid #d2b48c; /* light brown accent */
    position: fixed;
    top: 0; left: 0;
    height: 100%;
    padding: 20px;
  }

  .sidebar h2 {
    font-size: 22px;
    margin-bottom: 20px;
    text-align: center;
    color: #5C3317; /* darker brown */
  }

  .sidebar a {
    display: block;
    padding: 10px;
    margin: 5px 0;
    color: #5C3317;
    text-decoration: none;
    border-radius: 6px;
    font-size: 16px;
    transition: all 0.2s ease-in-out;
  }

  .sidebar a:hover {
    background: #f5f5f5; /* subtle white-gray hover */
    padding-left: 15px; /* slight slide effect */
  }

  .active-link {
    background: #f0e6d2; /* light cream/brown */
    font-weight: bold;
  }

  .content {
    margin-left: 240px; /* leave space for sidebar */
    padding: 20px;
  }
</style>

<div class="sidebar">
  <h2>🏖 Resort Panel</h2>
  <hr>
  <a href="resort_dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'resort_dashboard.php' ? 'active-link' : '' ?>">🏠 Dashboard</a>
  <a href="manage_bookings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_bookings.php' ? 'active-link' : '' ?>">📅 Manage Bookings</a>
  <a href="manage_accommodations.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_accommodations.php' ? 'active-link' : '' ?>">🛏 Accommodations</a>
  <a href="manage_amenities.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_reports.php' ? 'active-link' : '' ?>">🛠 Reports</a>
  <a href="resort_profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'resort_profile.php' ? 'active-link' : '' ?>">👤 Profile</a>

  <a href="logout.php">🚪 Logout</a>
</div>
