<?php
include 'db.php';

$sql = "SELECT resort_id, resort_name, latitude, longitude, logo 
        FROM resorts 
        WHERE status = 'active'";
$result = $conn->query($sql);

$resorts = [];
while ($row = $result->fetch_assoc()) {
    $resorts[] = $row;
}

header('Content-Type: application/json');
echo json_encode($resorts);
