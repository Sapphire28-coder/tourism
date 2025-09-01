<?php
session_start();

// ✅ Redirect if not logged in as tourist
if (!isset($_SESSION['tourist_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // db connection

$first_name = $_SESSION['first_name'];
$last_name  = $_SESSION['last_name'];
$email      = $_SESSION['email'];

// Fetch active resorts
$sql = "SELECT * FROM resorts WHERE status = 'active'";
$resorts = $conn->query($sql);
$resorts_data = [];
while ($row = $resorts->fetch_assoc()) {
    $resorts_data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Browse Resorts</title>
  <link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    body {
      margin: 0;
      font-family: 'Delius', cursive;
      background: #f5f5f5;
      display: flex;
      flex-direction: column;
      height: 100vh;
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
    .main {
      flex: 1;
      display: flex;
      height: calc(100vh - 60px);
    }
    .sidebar {
      width: 35%;
      background: white;
      border-right: 1px solid #ddd;
      overflow-y: auto;
      padding: 20px;
    }
    .map {
      flex: 1;
    }
    .resort-info img {
      max-width: 100%;
      border-radius: 8px;
      margin-bottom: 10px;
    }
    .resort-info h2 {
      margin: 5px 0;
      color: #8B4513;
    }
    .accommodation {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 10px 0;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 6px;
      background: #fafafa;
    }
    .accommodation img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 6px;
    }
    .accommodation-details {
      flex: 1;
    }
    .accommodation-details h4 {
      margin: 0;
      font-size: 16px;
    }
    .accommodation-details p {
      margin: 3px 0;
      font-size: 14px;
      color: #444;
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
    <h1>Browse Resorts</h1>
    <div>
      <a href="tourist_dashboard.php">Home</a>
      <a href="browse_resorts.php">Browse Resorts</a>
      <a href="my_bookings.php">My Bookings</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <!-- Main Section -->
  <div class="main">
    <!-- Resort Info Sidebar -->
    <div class="sidebar" id="resort-info">
      <h2>Select a resort on the map</h2>
      <p>Click on a resort logo to view details.</p>
    </div>

    <!-- Map -->
    <div class="map" id="map"></div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    // Resort data from PHP
    const resorts = <?php echo json_encode($resorts_data); ?>;

    // Initialize map
    const map = L.map('map').setView([10.50806644355898, 123.08953283730835], 13);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add resort markers
    resorts.forEach(resort => {
      if (resort.latitude && resort.longitude) {
        const logoIcon = L.icon({
          iconUrl: 'uploads/' + resort.logo,
          iconSize: [50, 50],
          iconAnchor: [25, 50],
          popupAnchor: [0, -50]
        });

        const marker = L.marker([resort.latitude, resort.longitude], { icon: logoIcon }).addTo(map);

        marker.on('click', () => {
          // Fetch accommodations dynamically
          fetch('get_accommodations.php?resort_id=' + resort.resort_id)
            .then(response => response.json())
            .then(data => {
              let accomHtml = '';
              data.forEach(acc => {
                accomHtml += `
                  <div class="accommodation">
                    <img src="${acc.picture ? acc.picture : 'default_room.png'}" alt="${acc.name}">
                    <div class="accommodation-details">
                      <h4>${acc.name}</h4>
                      <p>${acc.description}</p>
                      <p><strong>₱${acc.price}</strong></p>
                    </div>
                  </div>
                `;
              });

              document.getElementById('resort-info').innerHTML = `
                <div class="resort-info">
                  <img src="uploads/${resort.resort_picture}" alt="${resort.resort_name}">
                  <h2>${resort.resort_name}</h2>
                  <p><strong>Owner:</strong> ${resort.owner_name}</p>
                  <p><strong>Address:</strong> ${resort.resort_address}</p>
                  <p><strong>Contact:</strong> ${resort.contact_number}</p>
                  <p><strong>Email:</strong> ${resort.email}</p>
                  <p><strong>Amenities:</strong> ${resort.amenities}</p>
                  <p>${resort.description}</p>
                  <h3>Accommodations</h3>
                  ${accomHtml || '<p>No accommodations available.</p>'}
                  <a href="booknow.php?resort_id=${resort.resort_id}" class="btn">Book Now</a>
                </div>
              `;
            });
        });
      }
    });
  </script>
</body>
</html>
