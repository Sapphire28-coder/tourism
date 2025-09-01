<?php
session_start();
include 'db.php';

// Ensure resort is logged in
if (!isset($_SESSION['resort_id'])) {
    header("Location: login.php");
    exit();
}

$resort_id = $_SESSION['resort_id'];

// Check if a file was uploaded
if (isset($_FILES['resort_picture']) && $_FILES['resort_picture']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['resort_picture']['tmp_name'];
    $fileName = $_FILES['resort_picture']['name'];
    $fileSize = $_FILES['resort_picture']['size'];
    $fileType = $_FILES['resort_picture']['type'];

    // File extension
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($fileExtension, $allowedExtensions)) {
        // New unique file name
        $newFileName = "resort_" . $resort_id . "_" . time() . "." . $fileExtension;

        // Upload folder
        $uploadFileDir = 'uploads/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        $destPath = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // Save to DB
            $sql = "UPDATE resorts SET resort_picture = ? WHERE resort_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $newFileName, $resort_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Resort picture updated successfully!";
            } else {
                $_SESSION['message'] = "Database update failed.";
            }
        } else {
            $_SESSION['message'] = "Error moving uploaded file.";
        }
    } else {
        $_SESSION['message'] = "Invalid file type. Allowed: JPG, PNG, GIF, WEBP.";
    }
} else {
    $_SESSION['message'] = "No file uploaded.";
}

// Redirect back
header("Location: resort_profile.php");
exit();
?>
