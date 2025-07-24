<?php

include "connection.php";

if (!isset($_SESSION["login_attempts"])) {
  $_SESSION["login_attempts"] = 0;
}
if (!isset($_SESSION["last_attempt_time"])) {
  $_SESSION["last_attempt_time"] = 0;
}

$lockout_time = 180;
$current_time = time();

if (isset($_POST["signUpBtn"])) {
  $username = $_POST["signUpUsername"];
  $email = $_POST["signUpEmail"];
  $phone = $_POST["signUpPhone"];
  $password = $_POST["signUpPassword"];
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  $role_id = 1; // 1 for user

  $stmt = $connection->prepare("SELECT * FROM user WHERE phone = ? OR email = ?");
  $stmt->bind_param("ss", $phone, $email);
  $stmt->execute();
  $checkResult = $stmt->get_result();

  if ($checkArray = mysqli_fetch_array($checkResult)) {
    $checkEmail = $checkArray["email"];
    $checkphone = $checkArray["phone"];

    if ($phone == $checkphone) {
      echo "
        <script>
          window.alert('Phone number already exists!')
          window.location = 'index.php';
        </script>
      ";
    } elseif ($email == $checkEmail) {
      echo "
        <script>
          window.alert('Email already exists!')
          window.location = 'index.php';
        </script>
      ";
    }
  } else {
    $stmt = $connection->prepare("INSERT INTO user (username, email, phone, password, role_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $username, $email, $phone, $hashedPassword, $role_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
      $id = $connection->insert_id;

      $_SESSION["id"] = $id;
      $_SESSION["username"] = $username;
      $_SESSION["email"] = $email;

      echo "
        <script>
          window.alert('Registration Success')
          window.location = 'index.php'
        </script>
      ";
    } else {
      echo "
        <script>
          window.alert('Registration Failed!')
        </script>
      ";
    }
  }
} else if (isset($_POST["signInBtn"])) {

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

  $email = $_POST["signInEmail"];
  $password = $_POST["signInPassword"];

  $query = "SELECT * FROM user WHERE email = ?";
  $stmt = $connection->prepare($query);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user["password"])) {
      $_SESSION["id"] = $user["id"];
      $_SESSION["username"] = $user["username"];
      $_SESSION["email"] = $user["email"];
      $_SESSION["login_attempts"] = 0;
      $_SESSION["last_attempt_time"] = 0;

      echo "
        <script>
          window.alert('Login Success')
          window.location='index.php';
        </script>
      ";
    } else {
      $_SESSION["login_attempts"]++;
      $_SESSION["last_attempt_time"] = $current_time;

      echo "
        <script>
          alert('Incorrect password');
          window.location = 'index.php';
        </script>
      ";
    }
  } else {
    $_SESSION["login_attempts"]++;
    $_SESSION["last_attempt_time"] = $current_time;

    echo "
      <script>
        alert('User not found');
        window.location = 'index.php';
      </script>
    ";
  }
}

// Reset password
if (isset($_POST["resetPasswordBtn"])) {
  $email = $_POST["resetEmail"];
  $newPassword = $_POST["newPassword"];
  $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

  $stmt = $connection->prepare("SELECT id FROM user WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $stmt = $connection->prepare("UPDATE user SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
      echo "
        <script>
          alert('Password reset successful. You can now sign in.');
          window.location = 'index.php';
        </script>
      ";
    } else {
      echo "
        <script>
          alert('Failed to reset password.');
           window.location = 'index.php';
        </script>
      ";
    }
  } else {
    echo "
      <script>
        alert('No user found with that email.');
         window.location = 'index.php';
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
  <title>Auth</title>
</head>

<body>
  <div id="authModal" class="modal">
    <div class="auth-modal-content">
      <div class="auth-header">
        <span class="close" onclick="closeAuthModal()">&times;</span>
        <h2 id="authTitle">Sign Up</h2>
      </div>

      <div class="auth-form">
        <div class="signup-section active" id="signupSection">
          <form action="auth.php" method="post" id="signUpForm">
            <div class="form-group">
              <label for="signUpUsername">Username *</label>
              <input type="text" id="signUpUsername" name="signUpUsername" required placeholder="Enter username...">
            </div>

            <div class="form-group">
              <label for="signUpPhone">Phone Number *</label>
              <input type="text" id="signUpPhone" name="signUpPhone" required placeholder="Enter phone number...">
            </div>

            <div class="form-group">
              <label for="signUpEmail">Email Address *</label>
              <input type="email" id="signUpEmail" name="signUpEmail" required placeholder="Enter email...">
            </div>

            <div class="form-group password-wrapper">
              <label for="signUpPassword">Password *</label>
              <input type="password" class="password-input" name="signUpPassword" required
                placeholder="Enter password...">
              <span class="toggle-password" onclick="togglePassword(this)">
                <svg class="eye-icon eye-open" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960"
                  width="20px" fill="currentColor">
                  <path
                    d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z" />
                </svg>
                <svg class="eye-icon eye-closed hidden" xmlns="http://www.w3.org/2000/svg" height="20px"
                  viewBox="0 -960 960 960" width="20px" fill="currentColor">
                  <path
                    d="m644-428-58-58q9-47-27-88t-93-32l-58-58q17-8 34.5-12t37.5-4q75 0 127.5 52.5T660-500q0 20-4 37.5T644-428Zm128 126-58-56q38-29 67.5-63.5T832-500q-50-101-143.5-160.5T480-720q-29 0-57 4t-55 12l-62-62q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302Zm20 246L624-222q-35 11-70.5 16.5T480-200q-151 0-269-83.5T40-500q21-53 53-98.5t73-81.5L56-792l56-56 736 736-56 56ZM222-624q-29 26-53 57t-41 67q50 101 143.5 160.5T480-280q20 0 39-2.5t39-5.5l-36-38q-11 3-21 4.5t-21 1.5q-75 0-127.5-52.5T300-500q0-11 1.5-21t4.5-21l-84-82Zm319 93Zm-151 75Z" />
                </svg>
              </span>
            </div>

            <input type="submit" value="Sign Up" class="auth-submit-btn" name="signUpBtn" id="signUpSubmitBtn" />
          </form>
          <div class="auth-navigation">
            <p>Already have an account?</p>
            <span class="toggle-btn" id="signinToggle" onclick="switchForm('signin')">Sign In</span>
          </div>
        </div>

        <div class="signin-section" id="signinSection">
          <form action="auth.php" method="post" id="signInForm">
            <div class="form-group">
              <label for="signInEmail">Email Address *</label>
              <input type="email" id="signInEmail" name="signInEmail" required placeholder="Enter email...">
            </div>

            <div class="form-group password-wrapper">
              <label for="signInPassword">Password *</label>
              <input type="password" class="password-input" name="signInPassword" required
                placeholder="Enter password...">
              <span class="toggle-password" onclick="togglePassword(this)">
                <svg class="eye-icon eye-open" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960"
                  width="20px" fill="currentColor">
                  <path
                    d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z" />
                </svg>
                <svg class="eye-icon eye-closed hidden" xmlns="http://www.w3.org/2000/svg" height="20px"
                  viewBox="0 -960 960 960" width="20px" fill="currentColor">
                  <path
                    d="m644-428-58-58q9-47-27-88t-93-32l-58-58q17-8 34.5-12t37.5-4q75 0 127.5 52.5T660-500q0 20-4 37.5T644-428Zm128 126-58-56q38-29 67.5-63.5T832-500q-50-101-143.5-160.5T480-720q-29 0-57 4t-55 12l-62-62q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302Zm20 246L624-222q-35 11-70.5 16.5T480-200q-151 0-269-83.5T40-500q21-53 53-98.5t73-81.5L56-792l56-56 736 736-56 56ZM222-624q-29 26-53 57t-41 67q50 101 143.5 160.5T480-280q20 0 39-2.5t39-5.5l-36-38q-11 3-21 4.5t-21 1.5q-75 0-127.5-52.5T300-500q0-11 1.5-21t4.5-21l-84-82Zm319 93Zm-151 75Z" />
                </svg>
              </span>
            </div>

            <input type="submit" value="Sign In" class="auth-submit-btn" name="signInBtn" id="signInSubmitBtn" />
          </form>
          <div class="auth-navigation">
            <p>Don't have an account?</p>
            <span class="toggle-btn" id="signupToggle" onclick="switchForm('signup')">Sign Up</span>
          </div>
          <div class="auth-navigation">
            <span class="toggle-btn" style="font-size: 0.9em; color: #007bff; cursor: pointer;"
              onclick="switchForm('reset')">Forgot Password?</span>
          </div>
        </div>

        <div class="reset-section" id="resetSection">
          <form action="auth.php" method="post" id="resetForm">
            <div class="form-group">
              <label for="resetEmail">Enter your registered Email *</label>
              <input type="email" id="resetEmail" name="resetEmail" required placeholder="Enter your email...">
            </div>

            <div class="form-group password-wrapper">
              <label for="newPassword">New Password *</label>
              <input type="password" id="newPassword" name="newPassword" class="password-input" required
                placeholder="Enter new password...">
              <span class="toggle-password" onclick="togglePassword(this)">
                <svg class="eye-icon eye-open" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960"
                  width="20px" fill="currentColor">
                  <path
                    d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Zm0-300Zm0 220q113 0 207.5-59.5T832-500q-50-101-144.5-160.5T480-720q-113 0-207.5 59.5T128-500q50 101 144.5 160.5T480-280Z" />
                </svg>
                <svg class="eye-icon eye-closed hidden" xmlns="http://www.w3.org/2000/svg" height="20px"
                  viewBox="0 -960 960 960" width="20px" fill="currentColor">
                  <path
                    d="m644-428-58-58q9-47-27-88t-93-32l-58-58q17-8 34.5-12t37.5-4q75 0 127.5 52.5T660-500q0 20-4 37.5T644-428Zm128 126-58-56q38-29 67.5-63.5T832-500q-50-101-143.5-160.5T480-720q-29 0-57 4t-55 12l-62-62q41-17 84-25.5t90-8.5q151 0 269 83.5T920-500q-23 59-60.5 109.5T772-302Zm20 246L624-222q-35 11-70.5 16.5T480-200q-151 0-269-83.5T40-500q21-53 53-98.5t73-81.5L56-792l56-56 736 736-56 56ZM222-624q-29 26-53 57t-41 67q50 101 143.5 160.5T480-280q20 0 39-2.5t39-5.5l-36-38q-11 3-21 4.5t-21 1.5q-75 0-127.5-52.5T300-500q0-11 1.5-21t4.5-21l-84-82Zm319 93Zm-151 75Z" />
                </svg>
              </span>
            </div>

            <input type="submit" value="Reset Password" class="auth-submit-btn" name="resetPasswordBtn" />
          </form>

          <div class="auth-navigation">
            <p>Remembered your password?</p>
            <span class="toggle-btn" onclick="switchForm('signin')">Back to Sign In</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>

<script>
  const signInForm = document.getElementById("signInForm");
  const signInBtn = document.getElementById("signInSubmitBtn");

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

  function disableLoginWithTimer(seconds) {
    let remaining = seconds;
    signInBtn.disabled = true;
    signInBtn.value = `Try again in ${remaining}s`;

    const interval = setInterval(() => {
      remaining--;
      signInBtn.value = `Try again in ${remaining}s`;

      if (remaining <= 0) {
        clearInterval(interval);
        signInBtn.disabled = false;
        signInBtn.value = "Sign In";
        sessionStorage.removeItem("lockout");
      }
    }, 1000);
  }

  // If server set lockout in sessionStorage
  if (signInBtn) {
    const lockout = sessionStorage.getItem("lockout");
    if (lockout && parseInt(lockout) > 0) {
      disableLoginWithTimer(parseInt(lockout));
    }
  }
</script>