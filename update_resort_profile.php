<?php
session_start();
include 'db.php';

// Make sure resort is logged in
if (!isset($_SESSION['resort_id'])) {
    header("Location: login.php");
    exit();
}

$resort_id = $_SESSION['resort_id'];

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $resort_name    = $_POST['resort_name'] ?? '';
    $resort_address = $_POST['resort_address'] ?? '';
    $owner_name     = $_POST['owner_name'] ?? '';
    $amenities      = $_POST['amenities'] ?? '';
    $description    = $_POST['description'] ?? '';
    $email          = $_POST['email'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $status         = $_POST['status'] ?? '';
    $latitude       = $_POST['latitude'] ?? null;
    $longitude      = $_POST['longitude'] ?? null;

    // SQL Update
    $sql = "UPDATE resorts 
            SET resort_name=?, resort_address=?, owner_name=?, amenities=?, description=?, 
                email=?, contact_number=?, status=?, latitude=?, longitude=?
            WHERE resort_id=?";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            "ssssssssddi",
            $resort_name,
            $resort_address,
            $owner_name,
            $amenities,
            $description,
            $email,
            $contact_number,
            $status,
            $latitude,
            $longitude,
            $resort_id
        );

        if ($stmt->execute()) {
            $_SESSION['message'] = "Profile updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating profile: " . $stmt->error;
        }
    } else {
        $_SESSION['message'] = "SQL Error: " . $conn->error;
    }
}

// Redirect back
header("Location: resort_profile.php");
exit();
?>
