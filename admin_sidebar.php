<?php

include 'db.php';

// Block access if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all bookings with resort + accommodation names
$bookings = [];
$sql = "SELECT b.booking_id, b.reference_no, b.customer_name, b.customer_email,
               b.check_in, b.check_out, b.amount, b.status, b.created_at,
               r.resort_name,
               a.accommodation_name
        FROM bookings b
        JOIN resorts r ON b.resort_id = r.resort_id
        JOIN accommodations a ON b.accommodation_id = a.accommodation_id
        ORDER BY b.created_at DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $bookings = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Bookings</title>
  <link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Delius', cursive;
      background: #fff;
      color: #333;
    }

    .content {
      margin-left: 240px; /* space for sidebar */
      padding: 20px;
    }

    h1 {
      color: #5C3317;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background: #f0e6d2;
      color: #5C3317;
    }

    tr:hover {
      background: #f9f9f9;
    }

    .btn {
      padding: 6px 12px;
      background: #d2b48c;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      color: #5C3317;
      font-weight: bold;
      text-decoration: none;
    }

    .btn:hover {
      background: #c4a484;
    }

    .status {
      padding: 4px 8px;
      border-radius: 6px;
      font-weight: bold;
    }

    .pending { background: #ffeeba; color: #856404; }
    .confirmed { background: #c3e6cb; color: #155724; }
    .cancelled { background: #f5c6cb; color: #721c24; }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <?php include 'admin_sidebar.php'; ?>

  <!-- Main content -->
  <div class="content">
    <h1>Manage Bookings</h1>

    <?php if (count($bookings) > 0): ?>
      <table>
        <thead>
          <tr>
            <th>Booking Ref</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Resort</th>
            <th>Accommodation</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bookings as $b): ?>
            <tr>
              <td><?= htmlspecialchars($b['reference_no']) ?></td>
              <td><?= htmlspecialchars($b['customer_name']) ?></td>
              <td><?= htmlspecialchars($b['customer_email']) ?></td>
              <td><?= htmlspecialchars($b['resort_name']) ?></td>
              <td><?= htmlspecialchars($b['accommodation_name']) ?></td>
              <td><?= htmlspecialchars($b['check_in']) ?></td>
              <td><?= htmlspecialchars($b['check_out']) ?></td>
              <td>₱<?= number_format($b['amount'], 2) ?></td>
              <td>
                <span class="status <?= strtolower($b['status']) ?>">
                  <?= htmlspecialchars(ucfirst($b['status'])) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($b['created_at']) ?></td>
              <td>
                <a href="edit_booking.php?id=<?= $b['booking_id'] ?>" class="btn">Edit</a>
                <a href="delete_booking.php?id=<?= $b['booking_id'] ?>" class="btn" onclick="return confirm('Delete this booking?');">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No bookings found.</p>
    <?php endif; ?>
  </div>

</body>
</html>
