<?php
session_start();
include 'db.php';

// Block access if not logged in or not admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch tourists
$tourists = [];
$query = "SELECT * FROM tourist ORDER BY created_at DESC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $tourists = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Tourists</title>
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

    .profile-pic {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <?php include 'admin_sidebar.php'; ?>

  <!-- Main content -->
  <div class="content">
    <h1>Manage Tourists</h1>

    <?php if (count($tourists) > 0): ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Profile</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Contact</th>
            <th>Created At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tourists as $t): ?>
            <tr>
              <td><?= htmlspecialchars($t['tourist_id']) ?></td>
              <td>
                <?php if (!empty($t['profile_picture'])): ?>
                  <img src="<?= htmlspecialchars($t['profile_picture']) ?>" alt="Profile" class="profile-pic">
                <?php else: ?>
                  <img src="default-profile.png" alt="Profile" class="profile-pic">
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($t['first_name'] . " " . $t['last_name']) ?></td>
              <td><?= htmlspecialchars($t['email']) ?></td>
              <td><?= htmlspecialchars($t['contact_number']) ?></td>
              <td><?= htmlspecialchars($t['created_at']) ?></td>
              <td>
                <a href="edit_tourist.php?id=<?= $t['tourist_id'] ?>" class="btn">Edit</a>
                <a href="delete_tourist.php?id=<?= $t['tourist_id'] ?>" class="btn" onclick="return confirm('Delete this tourist?');">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No tourists found.</p>
    <?php endif; ?>
  </div>

</body>
</html>
