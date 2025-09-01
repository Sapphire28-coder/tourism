<?php
include 'db.php';

$resort_id = $_GET['resort_id'] ?? 0;

$sql = "SELECT accommodation_id, name, description, price, picture 
        FROM accommodations 
        WHERE resort_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resort_id);
$stmt->execute();
$result = $stmt->get_result();

$accommodations = [];
while ($row = $result->fetch_assoc()) {
    $accommodations[] = $row;
}

echo json_encode($accommodations);
