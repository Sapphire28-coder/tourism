<?php
session_start();
include 'db.php';

// Block access if not logged in or not resort
if (!isset($_SESSION['resort_id'])) {
    header("Location: signin.php");
    exit();
}

$resort_id = $_SESSION['resort_id'];

// Handle booking actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === "confirm") {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE booking_id = ? AND resort_id = ?");
        $stmt->bind_param("ii", $booking_id, $resort_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === "cancel") {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE booking_id = ? AND resort_id = ?");
        $stmt->bind_param("ii", $booking_id, $resort_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch bookings
$query = "SELECT b.booking_id, b.customer_name, b.customer_email, b.check_in, b.check_out, b.status, 
                 a.name AS accommodation_name
          FROM bookings b
          JOIN accommodations a ON b.accommodation_id = a.accommodation_id
          WHERE a.resort_id = ?
          ORDER BY b.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $resort_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<?php include 'resort_sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Bookings</title>
  <link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Delius', cursive;
      background: #f8f8f8;
      margin: 0;
    }
    .content {
      margin-left: 250px;
      padding: 20px;
      width: calc(100% - 250px);
      min-height: 100vh;
      box-sizing: border-box;
    }
    h2 {
      color: #8B4513;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      text-align: left;
      font-size: 14px;
    }
    th {
      background: #8B4513;
      color: white;
    }
    tr:nth-child(even) {
      background: #f9f9f9;
    }
    .status {
      padding: 5px 8px;
      border-radius: 5px;
      font-size: 12px;
      text-transform: capitalize;
    }
    .pending { background: #f1c40f; color: #fff; }
    .confirmed { background: #2ecc71; color: #fff; }
    .cancelled { background: #e74c3c; color: #fff; }
    .actions a {
      padding: 6px 10px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 12px;
      margin-right: 5px;
      color: white;
    }
    .confirm { background: #3498db; }
    .cancel { background: #e74c3c; }
  </style>
</head>
<body>
  <div class="content">
    <h2>Manage Bookings</h2>

    <table>
      <tr>
        <th>Reference No.</th>
        <th>Guest</th>
        <th>Email</th>
        <th>Accommodation</th>
        <th>Check-In</th>
        <th>Check-Out</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
      <?php while ($row = $bookings->fetch_assoc()): ?>
        <?php $ref_no = "RES-" . str_pad($row['booking_id'], 5, "0", STR_PAD_LEFT); ?>
        <tr>
          <td><?= $ref_no ?></td>
          <td><?= htmlspecialchars($row['customer_name']) ?></td>
          <td><?= htmlspecialchars($row['customer_email']) ?></td>
          <td><?= htmlspecialchars($row['accommodation_name']) ?></td>
          <td><?= $row['check_in'] ?></td>
          <td><?= $row['check_out'] ?></td>
          <td><span class="status <?= $row['status'] ?>"><?= $row['status'] ?></span></td>
          <td class="actions">
            <?php if ($row['status'] === "pending"): ?>
              <a href="?action=confirm&id=<?= $row['booking_id'] ?>" class="confirm">Confirm</a>
              <a href="?action=cancel&id=<?= $row['booking_id'] ?>" class="cancel" onclick="return confirm('Cancel this booking?')">Cancel</a>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</body>
</html>
