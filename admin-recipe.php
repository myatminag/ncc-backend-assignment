<?php

include "connection.php";

$search = $_GET['search'] ?? '';
$cuisine = $_GET['cuisine'] ?? '';
$difficulty = $_GET['difficulty'] ?? '';

$where = [];
$params = [];

if (!empty($search)) {
  $where[] = "r.name LIKE ?";
  $params[] = "%" . $search . "%";
}

if (!empty($cuisine)) {
  $where[] = "ct.name = ?";
  $params[] = $cuisine;
}

if (!empty($difficulty)) {
  $where[] = "dl.name = ?";
  $params[] = $difficulty;
}

$where_sql = '';
if (!empty($where)) {
  $where_sql = "WHERE " . implode(" AND ", $where);
}

$sql = "
  SELECT 
    r.id,
    r.name,
    r.description,
    r.recipe_image,
    r.total_time_minutes,
    r.cooking_time_minutes,
    r.prep_time_minutes,
    r.difficulty_level_id,
    r.cuisine_type_id,
    r.ingredients,
    r.status,
    dl.name AS difficulty_name,
    ct.name AS cuisine_type,
    GROUP_CONCAT(DISTINCT dp.name ORDER BY dp.name SEPARATOR ', ') AS recipe_dietary_preferences
  FROM recipes AS r
  JOIN difficulty_levels AS dl ON r.difficulty_level_id = dl.id
  JOIN cuisine_types AS ct ON r.cuisine_type_id = ct.id
  LEFT JOIN recipe_dietary_preferences AS rdp ON r.id = rdp.recipe_id
  LEFT JOIN dietary_preferences AS dp ON rdp.dietary_preference_id = dp.id
  $where_sql
  GROUP BY r.id, r.name, r.description, r.recipe_image, r.total_time_minutes, dl.name, ct.name, r.status
";


$stmt = $connection->prepare($sql);

if (!empty($params)) {
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$recipes = [];

while ($row = $result->fetch_assoc()) {
  $recipes[] = $row;
}

// Create and Edit Recipe
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save-btn"])) {
  $id = $_POST['recipe_id'] ?? null;
  $name = $_POST['recipeName'];
  $description = $_POST['recipeDescription'];
  $ingredients = $_POST['recipeIngredients'];
  $cuisine_type_id = $_POST['recipeCuisine'];
  $difficulty_level_id = $_POST['recipeDifficulty'];
  $prep_time = $_POST['recipePreparationTime'];
  $cook_time = $_POST['recipeCookingTime'];
  $status = $_POST['recipeStatus'];
  $dietary_ids = $_POST['recipeDietary'] ?? [];
  $total_time = $prep_time + $cook_time;

  $image_path = '';

  if (isset($_FILES['recipeImage']) && $_FILES['recipeImage']['error'] == 0) {
    $upload_dir = './images/';
    $image_path = $upload_dir . basename($_FILES['recipeImage']['name']);
    move_uploaded_file($_FILES['recipeImage']['tmp_name'], $image_path);
  }

  if ($id) {
    $stmt = $connection->prepare("UPDATE recipes SET name=?, description=?, ingredients=?, cuisine_type_id=?, difficulty_level_id=?, prep_time_minutes=?, cooking_time_minutes=?, total_time_minutes=?, status=? " . ($image_path ? ", recipe_image=?" : "") . " WHERE id=?");

    if ($image_path) {
      $stmt->bind_param("sssiiiiissi", $name, $description, $ingredients, $cuisine_type_id, $difficulty_level_id, $prep_time, $cook_time, $total_time, $status, $image_path, $id);
    } else {
      $stmt->bind_param("sssiiiiisi", $name, $description, $ingredients, $cuisine_type_id, $difficulty_level_id, $prep_time, $cook_time, $total_time, $status, $id);
    }

    $stmt->execute();

    $connection->query("DELETE FROM recipe_dietary_preferences WHERE recipe_id = $id");
  } else {
    $stmt = $connection->prepare("INSERT INTO recipes (name, description, ingredients, cuisine_type_id, difficulty_level_id, prep_time_minutes, cooking_time_minutes, total_time_minutes, status, recipe_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiiiiiss", $name, $description, $ingredients, $cuisine_type_id, $difficulty_level_id, $prep_time, $cook_time, $total_time, $status, $image_path);
    $stmt->execute();
    $id = $stmt->insert_id;
  }

  $insert_stmt = $connection->prepare("INSERT INTO recipe_dietary_preferences (recipe_id, dietary_preference_id) VALUES (?, ?)");

  foreach ($dietary_ids as $dietary_id) {
    $insert_stmt->bind_param("ii", $id, $dietary_id);
    $insert_stmt->execute();
  }

  header("Location: admin-recipe.php");
  exit;
}

// Delete Recipe
if (isset($_GET['delete'])) {
  $delete_id = intval($_GET['delete']);
  $connection->query("DELETE FROM recipe_dietary_preferences WHERE recipe_id = $delete_id");
  $connection->query("DELETE FROM recipes WHERE id = $delete_id");
  header("Location: admin-recipe.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/admin.css">
  <title>Recipe Management</title>
</head>

<body>

  <?php include "admin-sidebar.php"; ?>

  <section class="main-content">
    <div class="header">
      <h1>Recipe Management</h1>
      <div class="user-info">
        <span>Welcome, Admin</span>
        <div class="user-avatar">A</div>
      </div>
    </div>

    <div id="recipes-section" class="section">
      <div class="content-section">
        <div class="section-header">
          <h2 class="section-title">Recipe Management</h2>
          <button class="add-btn" onclick="openRecipeModal()">+ Add Recipe</button>
        </div>
        <div class="section-content">
          <form class="controls">
            <input type="text" name="search" class="search-box" placeholder="Search recipes..."
              value="<?= htmlspecialchars($search) ?>">

            <select name="cuisine" class="filter-select">
              <option value="">All Cuisines</option>
              <?php
              $category_result = mysqli_query($connection, "SELECT id, name FROM cuisine_types ORDER BY name ASC");
              $selected_category = $_GET['cuisine'] ?? '';

              if ($category_result && mysqli_num_rows($category_result) > 0) {
                while ($row = mysqli_fetch_assoc($category_result)) {
                  $category_name = htmlspecialchars($row['name']);
                  $selected = ($category_name === $selected_category) ? 'selected' : '';

                  echo "
                  <option value=\"$category_name\" $selected>" . ucfirst($category_name) . "</option>
                ";
                }
              }
              ?>
            </select>

            <select name="difficulty" class="filter-select">
              <option value="">All Difficulties</option>
              <?php
              $difficulty_result = mysqli_query($connection, "SELECT id, name FROM difficulty_levels ORDER BY name ASC");
              $selected_difficulty = $_GET['difficulty'] ?? '';

              if ($difficulty_result && mysqli_num_rows($difficulty_result) > 0) {
                while ($row = mysqli_fetch_assoc($difficulty_result)) {
                  $difficulty_level = htmlspecialchars($row['name']);
                  $selected = ($difficulty_level === $selected_difficulty) ? 'selected' : '';

                  echo "
                  <option value=\"$difficulty_level\" $selected>" . ucfirst($difficulty_level) . "</option>
                ";
                }
              }
              ?>
            </select>

            <button type="submit" class="btn-primary">Filter</button>
            <a href="admin-recipe.php" class="btn-secondary">
              Clear
            </a>
          </form>
          <table class="data-table" id="recipesTable">
            <thead>
              <tr>
                <th>Recipe Name</th>
                <th>Cuisine</th>
                <th>Difficulty</th>
                <th>Cook Time</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="recipesTableBody">
              <?php if (empty($recipes)): ?>
                <tr>
                  <td colspan="6">No recipes found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($recipes as $recipe): ?>
                  <tr>
                    <td>
                      <?= htmlspecialchars($recipe['name']) ?>
                    </td>
                    <td>
                      <?= htmlspecialchars($recipe['cuisine_type']) ?>
                    </td>
                    <td>
                      <span style="text-transform: capitalize;"><?= htmlspecialchars($recipe['difficulty_name']) ?></span>
                    </td>
                    <td>
                      <?= htmlspecialchars($recipe['total_time_minutes']) ?> min
                    </td>
                    <td>
                      <span
                        class="status-badge status-<?= htmlspecialchars($recipe['status']) ?>"><?= htmlspecialchars($recipe['status']) ?></span>
                    </td>
                    <td>
                      <button class="action-btn btn-edit" onclick="editRecipe(<?= $recipe['id'] ?>)">Edit</button>
                      <button class="action-btn btn-delete" onclick="deleteRecipe(<?= $recipe['id'] ?>)">Delete</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>

          </table>
        </div>
      </div>
    </div>
  </section>

  <div id="recipeModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="recipeModalTitle">Add New Recipe</h3>
        <button class="close" onclick="closeModal('recipeModal')">&times;</button>
      </div>
      <div class="modal-body">
        <form id="recipeForm" method="post" action="admin-recipe.php" enctype="multipart/form-data">
          <input type="hidden" name="recipe_id" id="recipe_id">

          <div class="form-group">
            <label class="form-label">Recipe Name</label>
            <input type="text" id="recipeName" name="recipeName" class="form-input" required
              placeholder="Enter recipe name...">
          </div>

          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea class="form-textarea" id="recipeDescription" name="recipeDescription" required
              placeholder="Enter description..."></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Ingredients</label>
            <textarea class="form-textarea" id="recipeIngredients" name="recipeIngredients" required
              placeholder="Enter ingredients..."></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Cuisine Type</label>
            <select class="form-select" id="recipeCuisine" name="recipeCuisine" required>
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
            <label class="form-label">Difficulty Level</label>
            <select class="form-select" id="recipeDifficulty" name="recipeDifficulty" required>
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

          <div class="form-group">
            <label class="form-label">Dietary Preferences</label>
            <select class="form-select" id="recipeDietary" name="recipeDietary" name="recipeDietary[]" multiple
              required>
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

          <div class="form-group">
            <label class="form-label">Preparation Time (minutes)</label>
            <input type="number" class="form-input" id="recipePreparationTime" name="recipePreparationTime" required
              placeholder="Enter preparation time...">
          </div>

          <div class="form-group">
            <label class="form-label">Cooking Time (minutes)</label>
            <input type="number" class="form-input" id="recipeCookingTime" name="recipeCookingTime" required
              placeholder="Enter cooking time...">
          </div>

          <div class="form-group">
            <label class="form-label">Status</label>
            <select class="form-select" id="recipeStatus" name="recipeStatus">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Recipe Image</label>
            <img id="uploadRecipeImage" width="200" height="200" style="object-fit: cover;">
            <input type="file" id="recipeImage" name="recipeImage" accept="image/*" class="form-input" required
              onchange="loadFile(event)">
          </div>

          <div class="form-actions">
            <button type="button" class="btn-secondary" onclick="closeModal('recipeModal')">Cancel</button>
            <input type="submit" class="btn-primary" name="save-btn" value="Save Recipe">
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>

<script>
  let recipes = <?= json_encode($recipes ?? []) ?>;

  function openRecipeModal(id = null) {
    const modal = document.getElementById('recipeModal');
    const title = document.getElementById('recipeModalTitle');
    const form = document.getElementById('recipeForm');

    if (id) {
      const recipe = recipes.find(r => r.id == id);
      if (!recipe) return;

      title.textContent = 'Edit Recipe';
      form.recipe_id.value = recipe.id;
      form.recipeName.value = recipe.name || '';
      form.recipeDescription.value = recipe.description || '';
      form.recipeIngredients.value = recipe.ingredients || '';
      form.recipeCuisine.value = recipe.cuisine_type_id || '';
      form.recipeDifficulty.value = recipe.difficulty_level_id || '';
      form.recipePreparationTime.value = recipe.prep_time_minutes || '';
      form.recipeCookingTime.value = recipe.cooking_time_minutes || '';
      form.recipeStatus.value = recipe.status || '';

      // For multi-select dietary preferences
      const dietaryField = form.recipeDietary;
      const dietaryList = (recipe.recipe_dietary_preferences || '').split(', ').map(item => item.trim());

      for (let option of dietaryField.options) {
        option.selected = dietaryList.includes(option.text);
      }

      if (recipe.recipe_image) {
        form.uploadRecipeImage.src = recipe.recipe_image;
        form.uploadRecipeImage.style.display = 'block';
      } else {
        form.uploadRecipeImage.style.display = 'none';
      }
    } else {
      title.textContent = 'Add New Recipe';
      form.reset();
      form.recipe_id.value = '';
      form.recipeImage.required = true;
      form.uploadRecipeImage.src = '';
      form.uploadRecipeImage.style.display = 'none';
    }

    modal.style.display = 'block';
    document.body.classList.add('modal-open');
  }

  function closeModal() {
    document.getElementById("recipeModal").style.display = 'none';
    document.body.classList.remove('modal-open');
    currentEditId = null;
  }

  function editRecipe(id) {
    openRecipeModal(id);
  }

  function deleteRecipe(id) {
    if (confirm("Are you sure you want to delete this recipe?")) {
      window.location.href = `admin-recipe.php?delete=${id}`;
    }
  }

  const loadFile = function (e) {
    const uploadImage = document.getElementById('uploadRecipeImage');
    uploadImage.src = URL.createObjectURL(event.target.files[0]);
    uploadImage.style.display = 'block';
  }
</script>