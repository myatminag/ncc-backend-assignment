<?php

include "connection.php";

if (!isset($_SESSION["login_attempts"])) {
  $_SESSION["login_attempts"] = 0;
}
if (!isset($_SESSION["last_attempt_time"])) {
  $_SESSION["last_attempt_time"] = 0;
}

$lockout_time = 60;
$current_time = time();

if (isset($_POST["signInBtn"])) {

  if ($_SESSION["login_attempts"] >= 3) {
    $remaining = $lockout_time - ($current_time - $_SESSION["last_attempt_time"]);
    if ($remaining > 0) {
      echo "
        <script>
          sessionStorage.setItem('lockout', '$remaining');
          alert('Too many failed attempts. Try again in $remaining seconds.');
          window.location='index.php';
        </script>
      ";
      exit;
    } else {
      // Reset after lockout expires
      $_SESSION["login_attempts"] = 0;
      $_SESSION["last_attempt_time"] = 0;
    }
  }

  $email = $_POST["email"];
  $password = $_POST["password"];

  $query = "SELECT * FROM user WHERE email = ?";
  $stmt = $connection->prepare($query);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user["password"])) {
      $_SESSION["id"] = $user["id"];
      $_SESSION["name"] = $user["username"];
      $_SESSION["email"] = $user["email"];
      $_SESSION["type"] = $user["role_id"];
      $_SESSION["login_attempts"] = 0;
      $_SESSION["last_attempt_time"] = 0;

      echo "
        <script>
          window.alert('Login Success')
          window.location='admin-dashboard.php';
        </script>
      ";
    } else {
      $_SESSION["login_attempts"]++;
      $_SESSION["last_attempt_time"] = $current_time;

      echo "
        <script>
          alert('Incorrect password');
        </script>
      ";
    }
  } else {
    $_SESSION["login_attempts"]++;
    $_SESSION["last_attempt_time"] = $current_time;

    echo "
      <script>
        alert('Account not found');
      </script>
    ";
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/admin.css">
  <title>Sign Up</title>
</head>

<body>
  <div class="container">
    <div class="auth-container">
      <div class="auth-header">
        <h1>Admin Dashboard</h1>
      </div>

      <div class="form-container">
        <form action="admin-sign-in.php" method="post">
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="text" class="form-input" name="email" required placeholder="Enter email address...">
          </div>

          <div class="form-group password-wrapper">
            <label class="form-label">Password *</label>
            <input type="password" class="password-input" name="password" required placeholder="Enter password...">
            <span class="toggle-password" onclick="togglePassword(this)">
              <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960"
                width="20px" fill="currentColor">
                <path
                  d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z" />
              </svg>
              <svg class="eye-closed hidden" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960"
                width="20px" fill="currentColor">
                <path
                  d="m644-428-58-58q9-47-27-88t-93-32l-58-58q17-8 34.5-12t37.5-4q75 0 127.5 52.5T660-500q0 20-4 37.5T644-428Zm128 126-58-56q38-29 67.5-63.5T832-500q-50-101-143.5-160.5T480-720q-29 0-57 4t-55 12l-62-62q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302Zm20 246L624-222q-35 11-70.5 16.5T480-200q-151 0-269-83.5T40-500q21-53 53-98.5t73-81.5L56-792l56-56 736 736-56 56ZM222-624q-29 26-53 57t-41 67q50 101 143.5 160.5T480-280q20 0 39-2.5t39-5.5l-36-38q-11 3-21 4.5t-21 1.5q-75 0-127.5-52.5T300-500q0-11 1.5-21t4.5-21l-84-82Zm319 93Zm-151 75Z" />
              </svg>
            </span>
          </div>

          <input type="submit" value="Sign In" class="btn-primary" style="width: 100%;" name="signInBtn" />
        </form>
        <div class="auth-navigation">
          <p>Don't have an account?</p>
          <a href="admin-sign-up.php">Sign Up</a>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
<script>
  function togglePassword(toggleElement) {
    const wrapper = toggleElement.closest('.password-wrapper');
    const input = wrapper.querySelector('.password-input');
    const eyeOpen = wrapper.querySelector('.eye-open');
    const eyeClosed = wrapper.querySelector('.eye-closed');

    if (input.type === 'password') {
      input.type = 'text';
      eyeOpen.classList.add('hidden');
      eyeClosed.classList.remove('hidden');
    } else {
      input.type = 'password';
      eyeOpen.classList.remove('hidden');
      eyeClosed.classList.add('hidden');
    }
  }
</script>