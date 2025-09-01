<?php
include 'db.php';

if (!isset($_GET['resort_id'])) {
    exit("Resort not specified");
}

$resort_id = intval($_GET['resort_id']);
$sql = "SELECT * FROM resorts WHERE resort_id = $resort_id LIMIT 1";
$res = $conn->query($sql);

if ($res->num_rows > 0) {
    $resort = $res->fetch_assoc();
    echo "<div class='resort-card'>";
    echo "<h3>" . htmlspecialchars($resort['resort_name']) . "</h3>";
    if ($resort['logo']) {
        echo "<img src='" . htmlspecialchars($resort['logo']) . "' alt='Logo'>";
    }
    echo "<p><b>Address:</b> " . htmlspecialchars($resort['address']) . "</p>";
    echo "<p><b>Email:</b> " . htmlspecialchars($resort['resort_email']) . "</p>";
    echo "<p><b>Contact:</b> " . htmlspecialchars($resort['contact_number']) . "</p>";
    echo "<a href='booknow.php?resort_id=" . $resort['resort_id'] . "' class='btn'>Book Now</a>";
    echo "</div>";
} else {
    echo "<p>No resort details found.</p>";
}
