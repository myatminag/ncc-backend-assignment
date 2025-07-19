<?php

include "connection.php";

if (isset($_SESSION["username"]) && isset($_SESSION["email"])) {
  $username = $_SESSION["username"];
  $email = $_SESSION["email"];
  $loggedInUserId = $_SESSION["id"];
} else {
  echo "
    <script>
      window.alert('You must be logged in to access this page. Redirecting you back...')
      window.history.back();
    </script>
  ";
  exit();
}

echo $email;

if (isset($_POST["share-btn"])) {
  $title = $_POST["title"] ?? '';
  $cookingTime = $_POST["cookingTime"] ?? '';
  $cuisine = $_POST["cuisine"] ?? '';
  $dietaryArray = $_POST["dietary"] ?? [];
  $difficulty = $_POST["difficulty"] ?? '';
  $description = $_POST["description"] ?? '';
  $ingredients = $_POST["ingredients"] ?? '';
  $recipePhoto = $_FILES["image"]["name"] ?? null;

  $image = null;
  if ($recipePhoto && is_uploaded_file($_FILES['image']['tmp_name'])) {
    $path = "./images/";
    $image = $path . basename($recipePhoto);
    move_uploaded_file($_FILES['image']['tmp_name'], $image);
  } else {
    $image = '';
  }

  $stmt = $connection->prepare("
    INSERT INTO cookbook_recipe (name, cooking_time, cuisine_type_id, difficulty_level_id, description, ingredients, recipe_photo, user_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
  ");

  if (!$stmt) {
    die("Prepare failed: " . $connection->error);
  }

  $stmt->bind_param("ssssssss", $title, $cookingTime, $cuisine, $difficulty, $description, $ingredients, $image, $loggedInUserId);
  $success = $stmt->execute();
  $stmt->close();

  $success = true;

  if ($success) {
    $recipeId = $connection->insert_id;

    if (!empty($dietaryArray)) {
      $stmt = $connection->prepare("INSERT INTO recipe_dietary_preferences (recipe_id, dietary_preference_id) VALUES (?, ?)");
      foreach ($dietaryArray as $dietaryId) {
        $stmt->bind_param("ii", $recipeId, $dietaryId);
        $stmt->execute();
      }
      $stmt->close();
    }

    echo "
      <script>
        alert('Recipe shared successfully!');
        window.location = 'cookbook.php';
      </script>
    ";
    exit;
  } else {
    echo "<script>alert('Failed to save recipe.');</script>";
  }
}

if (isset($_POST["tip-btn"])) {
  $tagTitle = $_POST["tagTitle"] ?? '';
  $cookingTips = $_POST["cookingTips"] ?? '';

  $stmt = $connection->prepare("
    INSERT INTO cooking_tips (tag, description, user_id)
    VALUES (?, ?, ?)
  ");

  if (!$stmt) {
    die("Prepare failed: " . $connection->error);
  }

  $stmt->bind_param("sss", $tagTitle, $cookingTips, $loggedInUserId);
  $success = $stmt->execute();
  $stmt->close();

  $success = true;

  if ($success) {
    echo "
      <script>
        alert('Cookip tip shared successfully!');

        window.location = 'cookbook.php';
      </script>
    ";
    exit;
  } else {
    echo "<script>alert('Failed to save recipe.');</script>";
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Cookbook</title>
</head>

<body>
  <?php include "cookie-consent.php" ?>

  <?php include "auth.php" ?>

  <?php include "header.php" ?>

  <section class="hero">
    <div class="hero-content">
      <h1>Community Cookbook</h1>
      <p>
        Share your culinary creations, discover amazing recipes from fellow food lovers, and connect with a passionate
        community of fusion cooking enthusiasts from around the world.
      </p>
      <div class="action-buttons">
        <button class="share-action-btn secondary" onclick="openSubmissionModal()">
          📝 Share Your Recipe
        </button>
        <button class="share-action-btn secondary" onclick="openTipModal()">
          💡 Share Tips
        </button>
      </div>
    </div>
  </section>

  <section class="featured-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Featured Community Recipes</h2>
        <p class="section-subtitle">Discover the most loved recipes shared by our amazing community members</p>
      </div>

      <div class="recipes-grid" id="recipesGrid">
        <?php
        $select_query = "
          SELECT 
            r.id,
            r.name,
            r.description,
            r.recipe_photo,
            r.cooking_time,
            r.user_id AS recipe_user_id,
            dl.name AS difficulty_name,
            ct.name AS cuisine_type,
            u.username AS username,
            GROUP_CONCAT(DISTINCT dp.name ORDER BY dp.name SEPARATOR ', ') AS recipe_dietary_preferences
          FROM cookbook_recipe AS r
          JOIN difficulty_levels AS dl ON r.difficulty_level_id = dl.id
          JOIN cuisine_types AS ct ON r.cuisine_type_id = ct.id
          JOIN user AS u ON r.user_id = u.id
          LEFT JOIN recipe_dietary_preferences AS rdp ON r.id = rdp.recipe_id
          LEFT JOIN dietary_preferences AS dp ON rdp.dietary_preference_id = dp.id
          GROUP BY r.id, r.name, r.description, r.recipe_photo, r.cooking_time, dl.name;
        ";

        $select_result = mysqli_query($connection, $select_query);

        if ($select_result && mysqli_num_rows($select_result) > 0) {
          while ($recipe = mysqli_fetch_assoc($select_result)) {
            $id = htmlspecialchars($recipe['id']);
            $name = htmlspecialchars($recipe['name']);
            $username = htmlspecialchars($recipe['username']);
            $user_id = htmlspecialchars($recipe['recipe_user_id']);
            $description = htmlspecialchars($recipe['description']);
            $recipe_photo = htmlspecialchars($recipe['recipe_photo']);
            $difficulty_level = htmlspecialchars($recipe['difficulty_name']);
            $cuisine_type = htmlspecialchars($recipe['cuisine_type']);
            $cooking_time = htmlspecialchars($recipe['cooking_time']);
            $dietary_preferences = htmlspecialchars($recipe['recipe_dietary_preferences'] ?? 'None');
            $dietary_tags = array_filter(array_map('trim', explode(',', $dietary_preferences)));

            echo "
              <div class='recipe-card'>
                <div class='recipe-image' style='background-image: url(\"{$recipe_photo}\");'>
                  <div class='recipe-author'>👤 $username</div>
                  <div class='recipe-difficulty difficulty-$difficulty_level'>" . ucfirst($difficulty_level) . "</div>
                </div>
                <div class='recipe-content'>
                  <h3 class='recipe-title'>$name</h3>
                  <p class='recipe-description'>$description</p>

                  <div class='recipe-meta'>
                    <div class='recipe-time'> 
                      <svg xmlns='http://www.w3.org/2000/svg' height='20px' viewBox='0 -960 960 960' width='24px' fill='#6c757d'><path d='M610-760q-21 0-35.5-14.5T560-810q0-21 14.5-35.5T610-860q21 0 35.5 14.5T660-810q0 21-14.5 35.5T610-760Zm0 660q-21 0-35.5-14.5T560-150q0-21 14.5-35.5T610-200q21 0 35.5 14.5T660-150q0 21-14.5 35.5T610-100Zm160-520q-21 0-35.5-14.5T720-670q0-21 14.5-35.5T770-720q21 0 35.5 14.5T820-670q0 21-14.5 35.5T770-620Zm0 380q-21 0-35.5-14.5T720-290q0-21 14.5-35.5T770-340q21 0 35.5 14.5T820-290q0 21-14.5 35.5T770-240Zm60-190q-21 0-35.5-14.5T780-480q0-21 14.5-35.5T830-530q21 0 35.5 14.5T880-480q0 21-14.5 35.5T830-430ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880v80q-134 0-227 93t-93 227q0 134 93 227t227 93v80Zm0-320q-33 0-56.5-23.5T400-480q0-5 .5-10.5T403-501l-83-83 56-56 83 83q4-1 21-3 33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Z'/></svg>
                      $cooking_time mins
                    </div>
                    <div class='recipe-servings'>
                      <svg xmlns='http://www.w3.org/2000/svg' height='20px' viewBox='0 -960 960 960' width='24px' fill='#6c757d'><path d='M40-440v-80h81q14-127 103-216t216-103v-41h80v41q127 14 216 103t103 216h81v80H40Zm163-80h554q-14-103-92.5-171.5T480-760q-106 0-184.5 68.5T203-520Zm372 80H320v240h222q21 0 40.5-7t35.5-21l166-137q-8-8-18-12t-21-6q-17-3-33 1t-30 15l-108 87H400v-80h146l44-36q5-3 7.5-8t2.5-11q0-10-7.5-17.5T575-440Zm-335 0h-80v280h80v-280Zm0 360h-80q-33 0-56.5-23.5T80-160v-280q0-33 23.5-56.5T160-520h415q85 0 164 29t127 98l27 41-223 186q-27 23-60 34.5T542-120H309q-11 18-29 29t-40 11Zm240-440Z'/></svg>
                      $cuisine_type
                    </div>
                  </div>

                  <div class='recipe-tags'>
            ";

            foreach ($dietary_tags as $tag) {
              echo "<span class='recipe-tag cuisine-tag'>" . htmlspecialchars($tag) . "</span>";
            }

            echo "
                  </div>
                  <div class='recipe-actions'>
                    <button class='action-btn view-recipe'>View Recipe</button>";
            if ($loggedInUserId == $user_id) {
              echo "
                <form method='POST' action='delete-recipe.php' style='flex: 1; width: 100%;'>
                  <input type='hidden' name='recipe_id' value='{$id}'>
                  <button type='submit' class='action-btn delete-recipe'style='width: 100%;'>Delete Recipe</button>
                </form>
              ";
            }
            echo "
                  </div>
                </div>
              </div>
            ";
          }
        } else {
          echo "<p>No recipes found.</p>";
        }
        ?>
      </div>
    </div>
  </section>

  <section class="tips-section" id="tips">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Community Cooking Tips</h2>
        <p class="section-subtitle">Learn from the collective wisdom of our culinary community</p>
      </div>

      <div class="tips-grid" id="communityTips">
        <?php

        $select_query = "
          SELECT 
            ct.id,
            ct.tag,
            ct.description,
            ct.user_id,
            u.username AS username
          FROM cooking_tips AS ct
          JOIN user AS u ON ct.user_id = u.id
          GROUP BY ct.id, ct.tag, ct.description;
        ";

        $select_result = mysqli_query($connection, $select_query);

        if ($select_result && mysqli_num_rows($select_result) > 0) {
          while ($cookingTips = mysqli_fetch_assoc($select_result)) {
            $id = htmlspecialchars($cookingTips['id']);
            $tag = htmlspecialchars($cookingTips['tag']);
            $description = htmlspecialchars($cookingTips['description']);
            $username = htmlspecialchars($cookingTips['username']);
            $userId = (int) $cookingTips['user_id'];
            $isOwner = $loggedInUserId === $userId;


            $initials = '';
            $nameParts = explode(' ', $username);
            foreach ($nameParts as $part) {
              if (!empty($part)) {
                $initials .= strtoupper($part[0]);
              }
            }

            $buttons = '';
            if ($isOwner) {
              $buttons = "
                <form method='POST' action='delete-tip.php'>
                  <input type='hidden' name='tip_id' value='{$id}'>
                  <button class='action-btn delete-recipe'>
                    <svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#c1121f'><path d='M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z'/></svg>
                  </button>
                </form>
              ";
            }

            echo "
              <div class='tip-card'>
                <div class='tip-header'>
                  <div class='tip-user'>
                    <div class='tip-avatar'>$initials</div>
                    <div class='tip-author'>$username</div>
                  </div>
                  $buttons
                </div>
                <div class='tip-content'>
                  $description
                </div>
                <div class='tip-category'>$tag</div>
              </div>
            ";
          }
        }
        ?>
      </div>
    </div>
  </section>

  <section class="guidelines-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Community Guidelines</h2>
        <p class="section-subtitle">Help us maintain a positive and supportive environment for all members</p>
      </div>

      <div class="guidelines-content">
        <h3>Recipe Sharing Guidelines</h3>
        <ul>
          <li>Share original recipes or properly credit the source</li>
          <li>Include clear, step-by-step instructions</li>
          <li>Provide accurate ingredient measurements and cooking times</li>
          <li>Use high-quality photos when possible</li>
          <li>Be respectful and constructive in your comments</li>
        </ul>

        <h3>Community Behavior</h3>
        <ul>
          <li>Treat all members with respect and kindness</li>
          <li>Provide helpful and constructive feedback</li>
          <li>Share knowledge and tips generously</li>
          <li>Report inappropriate content to moderators</li>
          <li>Celebrate diversity in cooking styles and cultures</li>
        </ul>
      </div>
    </div>
  </section>

  <div id="submissionModal" class="modal">
    <div class="recipe-modal-content">
      <div class="recipe-modal-header">
        <span class="recipe-modal-close" onclick="closeSubmissionModal()">&times;</span>
        <h2>Share Your Recipe</h2>
        <p>Add your culinary creation to our community cookbook</p>
      </div>
      <div class="recipe-modal-body">
        <form id="recipeSubmissionForm" method="post" action="cookbook.php" enctype="multipart/form-data">
          <div class="form-group">
            <label for="recipeTitle">Recipe Title *</label>
            <input type="text" id="recipeTitle" name="title" required placeholder="Enter recipe title...">
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="cookingTime">Cooking Time *</label>
              <input type="text" id="cookingTime" name="cookingTime" required placeholder="Enter cooking time...">
            </div>
            <div class="form-group">
              <label for="cuisine">Dietary Preferences *</label>
              <select id="cuisine" name="dietary[]" multiple required>
                <option value="">Select Dietary Preferences</option>
                <?php
                $query = "SELECT id, name FROM dietary_preferences";
                $result = mysqli_query($connection, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                    $id = htmlspecialchars($row['id']);
                    $name = htmlspecialchars($row['name']);

                    echo "<option value=\"$id\">$name</option>";
                  }
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="cuisine">Cuisine Type *</label>
              <select id="cuisine" name="cuisine" required>
                <option value="">Select Cuisine</option>
                <?php
                $query = "SELECT id, name FROM cuisine_types";
                $result = mysqli_query($connection, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                    $id = htmlspecialchars($row['id']);
                    $name = htmlspecialchars($row['name']);

                    echo "<option value=\"$id\">$name</option>";
                  }
                }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="difficulty">Difficulty *</label>
              <select id="difficulty" name="difficulty" required>
                <option value="">Select Difficulty</option>
                <?php
                $query = "SELECT id, name FROM difficulty_levels";
                $result = mysqli_query($connection, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                    $id = htmlspecialchars($row['id']);
                    $name = htmlspecialchars($row['name']);

                    echo "<option value=\"$id\" style='text-transform: capitalize;'>$name</option>";
                  }
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="description">Recipe Description *</label>
            <textarea id="description" name="description" required style="min-height: 150px;"
              placeholder="Enter recipe description"></textarea>
          </div>

          <div class="form-group">
            <label for="description">Ingredients *</label>
            <textarea id="description" name="ingredients" required style="min-height: 150px;"
              placeholder="Describe your recipe, its inspiration, and what makes it special..."></textarea>
          </div>

          <div class="form-group">
            <label for="recipeImage">Recipe Photo (Optional)</label>
            <input type="file" id="recipeImage" name="image" accept="image/*">
          </div>

          <input type="submit" class="submit-btn" name="share-btn" value="Share Recipe with Community">
        </form>
      </div>
    </div>
  </div>

  <div id="tipModal" class="modal">
    <div class="recipe-modal-content">
      <div class="recipe-modal-header">
        <span class="recipe-modal-close" onclick="closeTipModal()">&times;</span>
        <h2>Share Your Cooking Tips</h2>
        <p>Add your cooking tip to our community cookbook</p>
      </div>
      <div class="recipe-modal-body">
        <form id="recipeSubmissionForm" method="post" action="cookbook.php" enctype="multipart/form-data">
          <div class="form-group">
            <label for="tagTitle">Tag Title *</label>
            <input type="text" id="tagTitle" name="tagTitle" required placeholder="Enter tag title...">
          </div>

          <div class="form-group">
            <label for="cookingTips">Cooking Tips *</label>
            <textarea id="cookingTips" name="cookingTips" required style="min-height: 150px;"
              placeholder="Enter cooking tip..."></textarea>
          </div>

          <input type="submit" class="submit-btn" name="tip-btn" value="Share Cooking Tips">
        </form>
      </div>
    </div>
  </div>

  <?php include "footer.php" ?>
</body>

</html>

<script src="javascript/index.js"></script>
<script>
  function openSubmissionModal() {
    document.getElementById('submissionModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
  }

  function closeSubmissionModal() {
    document.getElementById('submissionModal').style.display = 'none';
    document.body.style.overflow = 'auto';
  }

  function openTipModal() {
    document.getElementById('tipModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
  }

  function closeTipModal() {
    document.getElementById('tipModal').style.display = 'none';
    document.body.style.overflow = 'auto';
  }

  window.onclick = function (event) {
    const submissionModal = document.getElementById('submissionModal');
    const tipModal = document.getElementById('tipModal');
    if (event.target === submissionModal) {
      closeSubmissionModal();
    }

    if (event.target === tipModal) {
      closeTipModal();
    }

  }
</script>