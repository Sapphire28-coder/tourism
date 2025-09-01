<!-- tourist_sidebar.php -->
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
    background: #f5f5f5;
    padding-left: 15px;
  }

  .active-link {
    background: #f0e6d2; /* light cream/brown */
    font-weight: bold;
  }

  .content {
    margin-left: 240px;
    padding: 20px;
  }
</style>

<div class="sidebar">
  <h2>🌍 Tourist Panel</h2>
  <hr>
  <a href="tourist_dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'tourist_dashboard.php' ? 'active-link' : '' ?>">🏠 Dashboard</a>
  <a href="tourist_profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'tourist_profile.php' ? 'active-link' : '' ?>">👤 My Profile</a>
  <a href="browse_resorts.php" class="<?= basename($_SERVER['PHP_SELF']) == 'browse_resorts.php' ? 'active-link' : '' ?>">🏖 Browse Resorts</a>
  <a href="my_bookings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'my_bookings.php' ? 'active-link' : '' ?>">📅 My Bookings</a>
  <a href="my_payments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'my_payments.php' ? 'active-link' : '' ?>">💳 Payments</a>
  <a href="my_reviews.php" class="<?= basename($_SERVER['PHP_SELF']) == 'my_reviews.php' ? 'active-link' : '' ?>">⭐ Reviews</a>

  <a href="logout.php">🚪 Logout</a>
</div>
