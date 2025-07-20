<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <header>
    <nav>
      <a href="/L5111/assignment/index.php" class="logo">🍽️ FoodFusion</a>
      <div class="nav-list-container">
        <div class="nav-lists">
          <ul class="nav-links">
            <li>
              <a href="about.php">About</a>
            </li>
            <li>
              <a href="recipe.php">Recipes</a>
            </li>
            <li>
              <a href="cookbook.php">Cookbook</a>
            </li>
            <li>
              <a href="contact.php">Contact</a>
            </li>
            <li>
              <a href="culinary.php">Culinary</a>
            </li>
            <li>
              <a href="educational.php">Educational</a>
            </li>
          </ul>
          <?php if (isset($_SESSION['username'])): ?>
            <form action="logout.php" method="POST">
              <button type="submit" class="join-btn">Logout</button>
            </form>
          <?php else: ?>
            <button class="join-btn" onclick="openAuthModal('signup')">Register</button>
          <?php endif; ?>
        </div>
        <div class="mobile-menu">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
    </nav>
  </header>
</body>

</html>