<?php
session_start();
include 'db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // ✅ Check if email already exists in admins
    $check = $conn->prepare("SELECT admin_id FROM admins WHERE email = ?");
    if ($check === false) {
        die("Query error: " . $conn->error);
    }
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        // ✅ Insert new admin
        $stmt = $conn->prepare("
            INSERT INTO admins (first_name, last_name, email, password, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ");
        if ($stmt === false) {
            die("Insert error: " . $conn->error);
        }
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $password);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Admin registered successfully!";
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
    $check->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Register</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 40px; }
        .container { max-width: 400px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
        h2 { text-align: center; }
        input, button { width: 100%; padding: 10px; margin: 8px 0; }
        button { background: #8B4513; color: white; border: none; cursor: pointer; }
        button:hover { background: #5C3317; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Registration</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
