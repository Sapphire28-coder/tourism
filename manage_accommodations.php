<?php
session_start();
include 'db.php';

// Block access if not logged in or not resort
if (!isset($_SESSION['resort_id'])) {
    header("Location: signin.php");
    exit();
}

$resort_id = $_SESSION['resort_id'];

// Handle Add Accommodation
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $capacity = $_POST['capacity'];
    $status = "available";

    // Handle picture upload
    $picture = null;
    if (!empty($_FILES['picture']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $picture = $targetDir . time() . "_" . basename($_FILES['picture']['name']);
        move_uploaded_file($_FILES['picture']['tmp_name'], $picture);
    }

    $stmt = $conn->prepare("INSERT INTO accommodations (resort_id, name, description, price, capacity, picture, status, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issdiss", $resort_id, $name, $description, $price, $capacity, $picture, $status);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete Accommodation
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM accommodations WHERE accommodation_id = ? AND resort_id = ?");
    $stmt->bind_param("ii", $id, $resort_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch Accommodations
$result = $conn->prepare("SELECT * FROM accommodations WHERE resort_id = ?");
$result->bind_param("i", $resort_id);
$result->execute();
$accommodations = $result->get_result();
?>

<?php include 'resort_sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Accommodations</title>
  <link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Delius', cursive;
      background: #f8f8f8;
      margin: 0;
    }
    .content {
      margin-left: 250px; /* keep clear of sidebar */
      padding: 25px;
      width: calc(100% - 250px);
      min-height: 100vh;
      box-sizing: border-box;
    }
    h2 {
      color: #8B4513;
      margin-bottom: 20px;
    }
    .layout {
      display: flex;
      gap: 25px;
      align-items: flex-start;
    }
    /* Compact Form */
    .form-card {
      flex: 1;
      max-width: 320px;
      background: #fff;
      border-radius: 12px;
      padding: 18px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .form-card h3 {
      margin-top: 0;
      margin-bottom: 12px;
      font-size: 18px;
      color: #5a3d1e;
    }
    .form-card input,
    .form-card textarea,
    .form-card button {
      width: 100%;
      padding: 8px 10px;
      margin: 6px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    .form-card textarea {
      resize: none;
      height: 70px;
    }
    .form-card button {
      background: #8B4513;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 14px;
      padding: 9px;
      transition: background 0.2s ease-in-out;
    }
    .form-card button:hover {
      background: #A0522D;
    }

    /* Accommodations Grid */
    .grid {
      flex: 3;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 18px;
    }
    .accommodation-card {
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
    }
    .accommodation-card img {
      width: 100%;
      height: 140px;
      object-fit: cover;
      background: #eee;
    }
    .placeholder {
      display:flex;
      align-items:center;
      justify-content:center;
      height:140px;
      border:2px dashed #ccc;
      font-size:36px;
      color:#bbb;
    }
    .card-body {
      padding: 12px;
      flex: 1;
    }
    .card-body h4 {
      margin: 4px 0;
      color: #333;
      font-size: 15px;
    }
    .card-body p {
      margin: 2px 0;
      font-size: 13px;
      color: #555;
    }
    .actions {
      margin-top: 10px;
      display: flex;
      gap: 6px;
    }
    .actions a {
      flex: 1;
      padding: 6px;
      border-radius: 6px;
      text-align: center;
      font-size: 13px;
      text-decoration: none;
      color: white;
    }
    .edit { background: #3498db; }
    .edit:hover { background: #2980b9; }
    .delete { background: #e74c3c; }
    .delete:hover { background: #c0392b; }
  </style>
</head>
<body>
  <div class="content">
    <h2>Manage Accommodations</h2>
    <div class="layout">
      
      <!-- Compact Add Form -->
      <div class="form-card">
        <h3>Add Accommodation</h3>
        <form method="POST" enctype="multipart/form-data">
          <input type="text" name="name" placeholder="Name" required>
          <textarea name="description" placeholder="Description" required></textarea>
          <input type="number" name="price" step="0.01" placeholder="Price (₱)" required>
          <input type="number" name="capacity" placeholder="Capacity" required>
          <input type="file" name="picture" accept="image/*">
          <button type="submit" name="add">+ Add Accommodation</button>
        </form>
      </div>

      <!-- Accommodation Cards -->
      <div class="grid">
        <?php while ($row = $accommodations->fetch_assoc()): ?>
          <div class="accommodation-card">
            <?php if ($row['picture']): ?>
              <img src="<?= $row['picture'] ?>" alt="Accommodation">
            <?php else: ?>
              <div class="placeholder">+</div>
            <?php endif; ?>
            <div class="card-body">
              <h4><?= htmlspecialchars($row['name']) ?></h4>
              <p><?= htmlspecialchars($row['description']) ?></p>
              <p><strong>Price:</strong> ₱<?= number_format($row['price'], 2) ?></p>
              <p><strong>Capacity:</strong> <?= $row['capacity'] ?></p>
              <div class="actions">
                <a href="edit_accommodation.php?id=<?= $row['accommodation_id'] ?>" class="edit">Edit</a>
                <a href="?delete=<?= $row['accommodation_id'] ?>" class="delete" onclick="return confirm('Delete this accommodation?')">Delete</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

    </div>
  </div>
</body>
</html>
