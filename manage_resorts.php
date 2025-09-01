<?php
session_start();
include 'db.php';

// Block access if not logged in or not admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch resorts
$resorts = [];
$query = "SELECT * FROM resorts ORDER BY created_at DESC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $resorts = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Resorts</title>
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
  </style>
</head>
<body>

  <!-- Sidebar -->
  <?php include 'admin_sidebar.php'; ?>

  <!-- Main content -->
  <div class="content">
    <h1>Manage Resorts</h1>

    <?php if (count($resorts) > 0): ?>
      <table>
        <thead>
          <tr>
            <th>Resort ID</th>
            <th>Resort Name</th>
            <th>Address</th>
            <th>Status</th>
            <th>Contact</th>
            <th>Created At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($resorts as $resort): ?>
            <tr>
              <td><?= htmlspecialchars($resort['resort_id']) ?></td>
              <td><?= htmlspecialchars($resort['resort_name']) ?></td>
              <td><?= htmlspecialchars($resort['address']) ?></td>
              <td><?= htmlspecialchars($resort['status']) ?></td>
              <td><?= htmlspecialchars($resort['contact_number']) ?></td>
              <td><?= htmlspecialchars($resort['created_at']) ?></td>
              <td>
                <a href="edit_resort.php?id=<?= $resort['resort_id'] ?>" class="btn">Edit</a>
                <a href="delete_resort.php?id=<?= $resort['resort_id'] ?>" class="btn" onclick="return confirm('Delete this resort?');">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No resorts found.</p>
    <?php endif; ?>
  </div>

</body>
</html>
