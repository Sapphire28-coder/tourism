<?php
session_start();
include 'db.php';

$error = "";

// 🔎 Detect admin table name: 'admins' or 'admin'
$adminTable = 'admins';
$tbl = $conn->query("
    SELECT TABLE_NAME 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME IN ('admins','admin')
    LIMIT 1
");
if ($tbl && $tbl->num_rows > 0) {
    $adminTable = $tbl->fetch_assoc()['TABLE_NAME'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = $_POST['role']; // tourist | resort | admin

    // ✅ Select correct table
    if ($role === "tourist") {
        $sql = "SELECT tourist_id, first_name, last_name, email, password FROM tourist WHERE email = ? LIMIT 1";
    } elseif ($role === "resort") {
        $sql = "SELECT resort_id, resort_name, email, password FROM resorts WHERE email = ? LIMIT 1";
    } elseif ($role === "admin") {
        // use detected admin table
        $sql = "SELECT admin_id, first_name, last_name, email, password FROM `$adminTable` WHERE email = ? LIMIT 1";
    } else {
        $sql = "";
    }

    if (!empty($sql)) {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Query error: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // ✅ Verify hashed password
            if (password_verify($password, $row['password'])) {

                if ($role === "tourist") {
                    $_SESSION['tourist_id'] = $row['tourist_id'];
                    $_SESSION['first_name'] = $row['first_name'];
                    $_SESSION['last_name']  = $row['last_name'];
                    $_SESSION['email']      = $row['email'];
                    header("Location: tourist_dashboard.php");
                    exit();

                } elseif ($role === "resort") {
                    $_SESSION['resort_id']   = $row['resort_id'];
                    $_SESSION['resort_name'] = $row['resort_name'];
                    $_SESSION['email']       = $row['email'];
                    header("Location: resort_dashboard.php");
                    exit();

                } elseif ($role === "admin") {
                    $_SESSION['admin_id'] = $row['admin_id']; // ✅ correct key
                    $_SESSION['email']    = $row['email'];
                    header("Location: admin_dashboard.php");
                    exit();
                }

            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found with that email.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Tourism</title>
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
      background: url('images/login.jpg') no-repeat center center/cover;
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
      width: 350px;
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
    input {
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
    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
    .signup-text {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
    }
    .signup-text a {
      color: #8B4513;
      text-decoration: none;
      font-weight: bold;
    }
    .signup-text a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
<div class="container">
  <!-- Left panel -->
  <div class="left">
    <div>
      <h1>Welcome Back!</h1>
      <p>Login to continue your travel journey.</p>
    </div>
  </div>

  <!-- Right panel -->
  <div class="right">
    <div class="form-box">
      <h2>Login</h2>

      <div class="tabs">
        <div class="tab active" onclick="showForm('tourist', this)">Tourist</div>
        <div class="tab" onclick="showForm('resort', this)">Resort</div>
        <div class="tab" onclick="showForm('admin', this)">Admin</div>
      </div>

      <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <!-- Tourist Login -->
      <form method="POST" id="touristForm">
        <input type="hidden" name="role" value="tourist">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
      </form>

      <!-- Resort Login -->
      <form method="POST" id="resortForm" style="display:none;">
        <input type="hidden" name="role" value="resort">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
      </form>

      <!-- Admin Login -->
      <form method="POST" id="adminForm" style="display:none;">
        <input type="hidden" name="role" value="admin">
        <input type="email" name="email" placeholder="Admin Email" required>
        <input type="password" name="password" placeholder="Admin Password" required>
        <button type="submit">Login</button>
      </form>

      <p class="signup-text">No account yet? <a href="signup.php">Sign up here</a></p>
    </div>
  </div>
</div>

<script>
  function showForm(role, el) {
    document.getElementById("touristForm").style.display = (role === 'tourist') ? "block" : "none";
    document.getElementById("resortForm").style.display  = (role === 'resort') ? "block" : "none";
    document.getElementById("adminForm").style.display   = (role === 'admin') ? "block" : "none";
    document.querySelectorAll(".tab").forEach(tab => tab.classList.remove("active"));
    el.classList.add("active");
  }
</script>
</body>
</html>
