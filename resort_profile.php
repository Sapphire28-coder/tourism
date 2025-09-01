<?php
session_start();
include 'db.php';

// 🔒 Block access if not logged in as resort
if (!isset($_SESSION['resort_id'])) {
    header("Location: login.php");
    exit();
}

$resort_id = $_SESSION['resort_id'];

// ================= FETCH RESORT DETAILS ==================
$sql = "SELECT * FROM resorts WHERE resort_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resort_id);
$stmt->execute();
$resort = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Resort Profile</title>
  <link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <style>
    body {
      margin: 0;
      font-family: "Delius", cursive, Arial, sans-serif;
      background: #f9f9f9;
    }
    .content {
      margin-left: 240px;
      padding: 20px;
    }
    .card {
      background: #fff;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .card h3 {
      color: #8B4513;
      margin-top: 0;
    }
    .image-boxes {
      display: flex;
      gap: 20px;
      margin-bottom: 20px;
    }
    .image-box {
      width: 200px;
      height: 200px;
      border: 2px dashed #ccc;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      background: #fafafa;
      cursor: pointer;
      position: relative;
    }
    .image-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .placeholder {
      font-size: 40px;
      color: #bbb;
    }
    .info p {
      margin: 6px 0;
      font-size: 15px;
    }
    .info strong {
      color: #333;
    }
    .edit-btn {
      display: inline-block;
      margin-top: 15px;
      padding: 10px 20px;
      background: #8B4513;
      color: #fff;
      border-radius: 6px;
      text-decoration: none;
      cursor: pointer;
    }
    .edit-btn:hover {
      background: #5C3317;
    }
    /* ===== MODAL STYLING ===== */
    .modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      width: 400px;
      max-width: 90%;
    }
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .close {
      font-size: 20px;
      cursor: pointer;
    }
    .modal form {
      display: flex;
      flex-direction: column;
    }
    .modal input, .modal textarea, .modal button {
      margin: 8px 0;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-family: "Delius", cursive;
    }
    .modal button {
      background: #8B4513;
      color: #fff;
      cursor: pointer;
    }
    .modal button:hover {
      background: #5C3317;
    }
    /* ===== MAP STYLING ===== */
    #map {
      height: 400px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <?php include 'resort_sidebar.php'; ?>

  <!-- Main Content -->
  <div class="content">
    <div class="card">
      <h3><?= htmlspecialchars($resort['resort_name']) ?> Profile</h3>

      <!-- Image boxes -->
      <div class="image-boxes">
        <!-- Resort Logo -->
        <div class="image-box" onclick="openModal('logoModal')">
          <?php if (!empty($resort['logo'])): ?>
            <img src="uploads/<?= htmlspecialchars($resort['logo']) ?>" alt="Logo">
          <?php else: ?>
            <span class="placeholder">➕</span>
          <?php endif; ?>
        </div>

        <!-- Resort Picture -->
        <div class="image-box" onclick="openModal('pictureModal')">
          <?php if (!empty($resort['resort_picture'])): ?>
            <img src="uploads/<?= htmlspecialchars($resort['resort_picture']) ?>" alt="Resort Picture">
          <?php else: ?>
            <span class="placeholder">➕</span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Resort Info -->
      <div class="info">
        <p><strong>Owner:</strong> <?= htmlspecialchars($resort['owner_name'] ?? 'Not provided') ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($resort['resort_address'] ?? 'Not provided') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($resort['email'] ?? 'Not provided') ?></p>
        <p><strong>Contact:</strong> <?= htmlspecialchars($resort['contact_number'] ?? 'Not provided') ?></p>
        <p><strong>Amenities:</strong> <?= htmlspecialchars($resort['amenities'] ?? 'Not provided') ?></p>
        <p><strong>Description:</strong> <?= !empty($resort['description']) ? nl2br(htmlspecialchars($resort['description'])) : 'No description yet' ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($resort['status'] ?? 'Not set') ?></p>
        <p><strong>Created At:</strong> <?= htmlspecialchars($resort['created_at'] ?? '-') ?></p>
      </div>

      <button class="edit-btn" onclick="openModal('editModal')">✏️ Edit Profile</button>
    </div>

    <!-- Map Section -->
    <div id="map"></div>
  </div>

  <!-- ===== MODALS ===== -->
  <div id="logoModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Upload Logo</h3>
        <span class="close" onclick="closeModal('logoModal')">&times;</span>
      </div>
      <form method="post" action="upload_logo.php" enctype="multipart/form-data">
        <input type="file" name="logo" required>
        <button type="submit">Upload</button>
      </form>
    </div>
  </div>

  <div id="pictureModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Upload Resort Picture</h3>
        <span class="close" onclick="closeModal('pictureModal')">&times;</span>
      </div>
      <form method="post" action="upload_resort_picture.php" enctype="multipart/form-data">
        <input type="file" name="resort_picture" required>
        <button type="submit">Upload</button>
      </form>
    </div>
  </div>

  <div id="editModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Edit Profile</h3>
        <span class="close" onclick="closeModal('editModal')">&times;</span>
      </div>
      <form method="post" action="update_resort_profile.php">
        <input type="text" name="owner_name" placeholder="Owner Name" value="<?= htmlspecialchars($resort['owner_name'] ?? '') ?>">
        <input type="text" name="resort_address" placeholder="Address" value="<?= htmlspecialchars($resort['resort_address'] ?? '') ?>">
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($resort['email'] ?? '') ?>">
        <input type="text" name="contact_number" placeholder="Contact Number" value="<?= htmlspecialchars($resort['contact_number'] ?? '') ?>">
        <textarea name="amenities" placeholder="Amenities"><?= htmlspecialchars($resort['amenities'] ?? '') ?></textarea>
        <textarea name="description" placeholder="Description"><?= htmlspecialchars($resort['description'] ?? '') ?></textarea>
        <button type="submit">Save Changes</button>
      </form>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    function openModal(id) {
      document.getElementById(id).style.display = 'flex';
    }
    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    // ===== Leaflet Map =====
    var map = L.map('map').setView([10.50806644355898, 123.08953283730835], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Custom icon with resort logo
    var logoIcon = L.icon({
      iconUrl: "uploads/<?= !empty($resort['logo']) ? htmlspecialchars($resort['logo']) : 'default.png' ?>",
      iconSize: [50, 50],
      iconAnchor: [25, 50],
      popupAnchor: [0, -50]
    });

    L.marker([10.50806644355898, 123.08953283730835], { icon: logoIcon })
      .addTo(map)
      .bindPopup("<b><?= htmlspecialchars($resort['resort_name']) ?></b><br><?= htmlspecialchars($resort['resort_address'] ?? '') ?>");
  </script>
</body>
</html>
