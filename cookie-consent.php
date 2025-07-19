<?php

if (isset($_POST['acceptCookies'])) {
  setcookie('cookiesAccepted', 'true', time() + (24 * 60 * 60), "/", "", false, true);
  header("Location: index.php");
  exit();
}

$cookiesAccepted = isset($_COOKIE['cookiesAccepted']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cookie Consent</title>
</head>

<body>
  <?php if (!$cookiesAccepted): ?>
    <div class="cookie-consent show">
      <p>We use cookies at FoodFusion to enhance your experience, personalize recipe recommendations and improve site
        functionality.
        <a href="privacy-policy.php" style="color: #ff6b6b;">Learn more</a>
      </p>
      <form method="POST">
        <button type="submit" name="acceptCookies">Accept</button>
      </form>
    </div>
  <?php endif; ?>
</body>

</html>