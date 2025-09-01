<?php
session_start();

// ✅ Tourist login check
if (!isset($_SESSION['tourist_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Get logged in tourist info
$customer_name  = $_SESSION['first_name'] . " " . $_SESSION['last_name'];
$customer_email = $_SESSION['email'];

// Get resort ID
if (!isset($_GET['resort_id'])) {
    die("Resort not specified.");
}
$resort_id = intval($_GET['resort_id']);

// Fetch resort info
$resort_sql = "SELECT * FROM resorts WHERE resort_id = ?";
$stmt = $conn->prepare($resort_sql);
$stmt->bind_param("i", $resort_id);
$stmt->execute();
$resort = $stmt->get_result()->fetch_assoc();

if (!$resort) {
    die("Resort not found.");
}

// Fetch accommodations
$acc_sql = "SELECT * FROM accommodations WHERE resort_id = ?";
$stmt = $conn->prepare($acc_sql);
$stmt->bind_param("i", $resort_id);
$stmt->execute();
$accommodations = $stmt->get_result();

// ✅ Handle booking form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accommodation_id = $_POST['accommodation_id'] ?? null;
    $check_in  = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    if (empty($accommodation_id) || empty($check_in) || empty($check_out)) {
        echo "<script>alert('Please complete all fields.');</script>";
    } elseif ($check_in >= $check_out) {
        echo "<script>alert('Check-out date must be after check-in date.');</script>";
    } else {
        // Check if accommodation already booked for these dates
        $overlap_sql = "
            SELECT * FROM bookings 
            WHERE accommodation_id = ? 
              AND status IN ('pending', 'confirmed') 
              AND (
                    (check_in <= ? AND check_out > ?) OR
                    (check_in < ? AND check_out >= ?) OR
                    (check_in >= ? AND check_out <= ?)
                  )";
        $stmt = $conn->prepare($overlap_sql);
        $stmt->bind_param("issssss", $accommodation_id, $check_in, $check_in, $check_out, $check_out, $check_in, $check_out);
        $stmt->execute();
        $conflict = $stmt->get_result();

        if ($conflict->num_rows > 0) {
            echo "<script>alert('Sorry, this accommodation is already booked for the selected dates.');</script>";
        } else {
            // Get accommodation price
            $price_sql = "SELECT price FROM accommodations WHERE accommodation_id = ?";
            $stmt = $conn->prepare($price_sql);
            $stmt->bind_param("i", $accommodation_id);
            $stmt->execute();
            $price_row = $stmt->get_result()->fetch_assoc();
            $price_per_night = $price_row ? $price_row['price'] : 0;

            // Calculate number of nights
            $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
            $amount = $price_per_night * $nights;

            // Generate reference number
            $reference_no = "BNK-" . date("Ymd") . "-" . rand(1000, 9999);

            $sql = "INSERT INTO bookings (reference_no, resort_id, accommodation_id, customer_name, customer_email, check_in, check_out, amount, status, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siissssd", $reference_no, $resort_id, $accommodation_id, $customer_name, $customer_email, $check_in, $check_out, $amount);

            if ($stmt->execute()) {
                echo "<script>alert('Booking submitted successfully! Reference: $reference_no'); window.location='my_bookings.php';</script>";
                exit();
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book Now - <?php echo htmlspecialchars($resort['resort_name']); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Delius', cursive;
      margin: 0;
      background: #f9fafb;
      color: #333;
    }
    .navbar {
      background: #8B4513;;
      color: white;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .navbar a {
      color: white;
      text-decoration: none;
      margin-left: 15px;
      font-weight: 500;
    }
    .container {
      max-width: 1100px;
      margin: 30px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .resort-header {
      display: flex;
      gap: 20px;
      align-items: flex-start;
    }
    .resort-header img {
      width: 300px;
      height: 200px;
      object-fit: cover;
      border-radius: 12px;
    }
    .resort-details {
      flex: 1;
    }
    h2 {
      color: #1f2937;
      margin: 0 0 10px;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }
    .card {
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      overflow: hidden;
      background: #fff;
      transition: 0.2s;
      cursor: pointer;
    }
    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }
    .card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
    }
    .card-body {
      padding: 15px;
    }
    .card-body h4 {
      margin: 0 0 5px;
      font-size: 18px;
      color: #111827;
    }
    .card-body p {
      margin: 3px 0;
      font-size: 14px;
      color: #4b5563;
    }
    .card-body strong {
      color: #10b981;
    }
    .booking-container {
      display: flex;
      gap: 30px;
      margin-top: 30px;
    }
    .form-section {
      flex: 2;
      padding: 20px;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      background: #f9fafb;
    }
    .form-group {
      margin-bottom: 15px;
    }
    label {
      font-weight: 600;
      margin-bottom: 6px;
      display: block;
    }
    input[type="date"] {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #d1d5db;
      font-size: 14px;
    }
    .btn {
      background: #2563eb;
      color: white;
      padding: 12px 24px;
      text-decoration: none;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      font-size: 15px;
      transition: background 0.2s;
    }
    .btn:hover {
      background: #1e40af;
    }
    .summary-section {
      flex: 1;
      padding: 20px;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      background: #ffffff;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .summary-section h3 {
      margin-top: 0;
      color: #1f2937;
    }
    .summary-item {
      margin: 8px 0;
    }
    .summary-item strong {
      color: #111827;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <div class="navbar">
    <div><strong>Resort Booking</strong></div>
    <div>
      <a href="tourist_dashboard.php">Home</a>
      <a href="browse_resorts.php">Browse Resorts</a>
      <a href="my_bookings.php">My Bookings</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <div class="container">
    <div class="resort-header">
      <img src="uploads/<?php echo $resort['resort_picture']; ?>" alt="Resort">
      <div class="resort-details">
        <h2><?php echo htmlspecialchars($resort['resort_name']); ?></h2>
        <p><strong>Address:</strong> <?php echo $resort['resort_address']; ?></p>
        <p><strong>Contact:</strong> <?php echo $resort['contact_number']; ?></p>
        <p><strong>Email:</strong> <?php echo $resort['email']; ?></p>
        <p><strong>Amenities:</strong> <?php echo $resort['amenities']; ?></p>
        <p><?php echo $resort['description']; ?></p>
      </div>
    </div>

    <div class="booking-container">
      <!-- Left: Form -->
      <div class="form-section">
        <h3>Choose Accommodation</h3>
        <form method="POST" id="bookingForm">
          <div class="grid">
            <?php mysqli_data_seek($accommodations, 0); while ($acc = $accommodations->fetch_assoc()) { ?>
              <label class="card">
                <input type="radio" 
                       name="accommodation_id" 
                       value="<?php echo $acc['accommodation_id']; ?>" 
                       data-name="<?php echo htmlspecialchars($acc['name']); ?>"
                       data-price="<?php echo $acc['price']; ?>"
                       required hidden>
                <img src="<?php echo $acc['picture'] ?: 'default_room.png'; ?>" alt="">
                <div class="card-body">
                  <h4><?php echo $acc['name']; ?></h4>
                  <p><?php echo $acc['description']; ?></p>
                  <p><strong>₱<?php echo $acc['price']; ?> / night</strong></p>
                </div>
              </label>
            <?php } ?>
          </div>

          <div class="form-group">
            <label>Check-in Date:</label>
            <input type="date" name="check_in" id="checkIn" required>
          </div>
          <div class="form-group">
            <label>Check-out Date:</label>
            <input type="date" name="check_out" id="checkOut" required>
          </div>
          <button type="submit" class="btn">Confirm Booking</button>
        </form>
      </div>

      <!-- Right: Summary -->
      <div class="summary-section">
        <h3>Booking Summary</h3>
        <p class="summary-item"><strong>Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
        <p class="summary-item"><strong>Email:</strong> <?php echo htmlspecialchars($customer_email); ?></p>
        <p class="summary-item"><strong>Resort:</strong> <?php echo htmlspecialchars($resort['resort_name']); ?></p>
        <hr>
        <p class="summary-item"><strong>Accommodation:</strong> <span id="summaryAccommodation">Not selected</span></p>
        <p class="summary-item"><strong>Check-in:</strong> <span id="summaryCheckIn">-</span></p>
        <p class="summary-item"><strong>Check-out:</strong> <span id="summaryCheckOut">-</span></p>
        <p class="summary-item"><strong>Nights:</strong> <span id="summaryNights">0</span></p>
        <p class="summary-item"><strong>Total:</strong> ₱<span id="summaryTotal">0.00</span></p>
      </div>
    </div>
  </div>

  <script>
    const radios = document.querySelectorAll('input[name="accommodation_id"]');
    const checkInInput = document.getElementById('checkIn');
    const checkOutInput = document.getElementById('checkOut');

    let selectedPrice = 0;

    radios.forEach(radio => {
      radio.addEventListener('change', () => {
        document.getElementById('summaryAccommodation').textContent = radio.dataset.name;
        selectedPrice = parseFloat(radio.dataset.price);
        updateSummary();
      });
    });

    [checkInInput, checkOutInput].forEach(input => {
      input.addEventListener('change', updateSummary);
    });

    function updateSummary() {
      const checkIn = checkInInput.value;
      const checkOut = checkOutInput.value;

      document.getElementById('summaryCheckIn').textContent = checkIn || "-";
      document.getElementById('summaryCheckOut').textContent = checkOut || "-";

      if (checkIn && checkOut && selectedPrice > 0) {
        const nights = (new Date(checkOut) - new Date(checkIn)) / (1000*60*60*24);
        if (nights > 0) {
          document.getElementById('summaryNights').textContent = nights;
          document.getElementById('summaryTotal').textContent = (nights * selectedPrice).toFixed(2);
        } else {
          document.getElementById('summaryNights').textContent = 0;
          document.getElementById('summaryTotal').textContent = "0.00";
        }
      }
    }
  </script>
</body>
</html>
