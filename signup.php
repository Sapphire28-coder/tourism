<?php
session_start();
include 'db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role            = $_POST['role']; // tourist or resort
    $first_name      = trim($_POST['first_name']);
    $last_name       = trim($_POST['last_name']);
    $email           = trim($_POST['email']);
    $contact_number  = trim($_POST['contact_number']);
    $password        = trim($_POST['password']);
    $hashedPassword  = password_hash($password, PASSWORD_DEFAULT); // ✅ hash password

    if ($role === "tourist") {
        $sql = "INSERT INTO tourist (first_name, last_name, email, contact_number, password, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssss", $first_name, $last_name, $email, $contact_number, $hashedPassword);
            if ($stmt->execute()) {
                $success = "Tourist account created successfully!";
            } else {
                $error = "Error: " . $stmt->error;
            }
        } else {
            $error = "Query error: " . $conn->error;
        }

    } elseif ($role === "resort") {
        $resort_name   = trim($_POST['resort_name']);
        $resort_address= trim($_POST['resort_address']);
        $owner_name    = trim($_POST['owner_name']);
        $description   = trim($_POST['description']);

        $sql = "INSERT INTO resorts (resort_name, resort_address, owner_name, description, email, contact_number, password, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssss", $resort_name, $resort_address, $owner_name, $description, $email, $contact_number, $hashedPassword);
            if ($stmt->execute()) {
                $success = "Resort registered successfully! Waiting for approval.";
            } else {
                $error = "Error: " . $stmt->error;
            }
        } else {
            $error = "Query error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up - Tourism</title>
  <link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Delius', cursive;
      background: #f5f5f5;
    }
    .container {
      display: flex;
      height: 100vh;
    }
    .left {
      flex: 1;
      background: url('images/signup.jpg') no-repeat center center/cover;
      display: flex;
      justify-content: center;
      align-items: center;
      color: white;
      text-align: center;
      padding: 40px;
    }
    .left h1 {
      font-size: 2.5rem;
    }
    .right {
      flex: 1;
      background: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .form-box {
      width: 400px;
      padding: 20px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #8B4513;
    }
    .tabs {
      display: flex;
      margin-bottom: 20px;
      border-bottom: 2px solid #ddd;
    }
    .tab {
      flex: 1;
      padding: 10px;
      text-align: center;
      cursor: pointer;
      font-weight: bold;
      color: #555;
    }
    .tab.active {
      color: #8B4513;
      border-bottom: 2px solid #8B4513;
    }
    input, textarea {
      width: 100%;
      padding: 12px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-sizing: border-box;
      font-family: 'Delius', cursive;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #8B4513;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 10px;
      font-size: 16px;
      font-family: 'Delius', cursive;
    }
    button:hover {
      background: #5C3317;
    }
    .message {
      text-align: center;
      margin-bottom: 10px;
      font-weight: bold;
    }
    .error { color: red; }
    .success { color: green; }
    .login-text {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
    }
    .login-text a {
      color: #8B4513;
      text-decoration: none;
      font-weight: bold;
    }
    .login-text a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="left">
    <div>
      <h1>Join Us!</h1>
      <p>Create your account and start your journey.</p>
    </div>
  </div>

  <div class="right">
    <div class="form-box">
      <h2>Sign Up</h2>

      <div class="tabs">
        <div class="tab active" onclick="showForm('tourist', this)">Tourist</div>
        <div class="tab" onclick="showForm('resort', this)">Resort</div>
      </div>

      <?php if ($error): ?>
        <p class="message error"><?= $error ?></p>
      <?php elseif ($success): ?>
        <p class="message success"><?= $success ?></p>
      <?php endif; ?>

      <!-- Tourist Signup -->
      <form method="POST" id="touristForm">
        <input type="hidden" name="role" value="tourist">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="contact_number" placeholder="Contact Number" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign Up</button>
      </form>

      <!-- Resort Signup -->
      <form method="POST" id="resortForm" style="display:none;">
        <input type="hidden" name="role" value="resort">
        <input type="text" name="resort_name" placeholder="Resort Name" required>
        <input type="text" name="resort_address" placeholder="Resort Address" required>
        <input type="text" name="owner_name" placeholder="Owner Name" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="contact_number" placeholder="Contact Number" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register Resort</button>
      </form>

      <p class="login-text">Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </div>
</div>

<script>
  function showForm(role, el) {
    document.getElementById("touristForm").style.display = (role === 'tourist') ? "block" : "none";
    document.getElementById("resortForm").style.display  = (role === 'resort') ? "block" : "none";
    document.querySelectorAll(".tab").forEach(tab => tab.classList.remove("active"));
    el.classList.add("active");
  }
</script>
</body>
</html>
