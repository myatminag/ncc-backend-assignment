<?php

session_start();

include "connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['recipe_id'])) {
  $recipe_id = intval($_POST['recipe_id']);
  $user_id = $_SESSION['id'];

  $check_query = "SELECT * FROM cookbook_recipe WHERE id = ? AND user_id = ?";
  $stmt = $connection->prepare($check_query);
  $stmt->bind_param("ii", $recipe_id, $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows === 1) {
    $delete_query = "DELETE FROM cookbook_recipe WHERE id = ?";
    $delete_stmt = $connection->prepare($delete_query);
    $delete_stmt->bind_param("i", $recipe_id);

    if ($delete_stmt->execute()) {
      echo "
        <script>
          alert('Recipe deleted successfully.'); 
          window.location.href='cookbook.php';
        </script>
      ";
    } else {
      echo "
        <script>
          alert('Failed to delete recipe!'); 
          window.location.href='cookbook.php';
        </script>
      ";
    }

    $delete_stmt->close();
  } else {
    echo "
      <script>
        alert('You do not have permission to delete this recipe!'); 
        window.location.href='cookbook.php';
      </script>
    ";
  }

  $stmt->close();
} else {
  echo "
    <script>
      alert('Internal Server Error!'); 
      window.location.href='cookbook.php';
    </script>
  ";
}

?>