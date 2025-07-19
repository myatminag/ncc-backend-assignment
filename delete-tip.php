<?php

session_start();

include "connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tip_id'])) {
  $tip_id = intval($_POST['tip_id']);
  $user_id = $_SESSION['id'];

  $check_query = "SELECT * FROM cooking_tips WHERE id = ? AND user_id = ?";
  $stmt = $connection->prepare($check_query);
  $stmt->bind_param("ii", $tip_id, $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows === 1) {
    $delete_query = "DELETE FROM cooking_tips WHERE id = ?";
    $delete_stmt = $connection->prepare($delete_query);
    $delete_stmt->bind_param("i", $tip_id);

    if ($delete_stmt->execute()) {
      echo "
        <script>
          alert('Cooking tip deleted successfully.'); 
          window.location.href='cookbook.php';
        </script>
      ";
    } else {
      echo "
         <script>
          alert('Failed to delete cooking tip!'); 
          window.location.href='cookbook.php';
        </script>
      ";
    }

    $delete_stmt->close();
  } else {
    echo "
      <script>
        alert('You do not have permission to delete this tip!'); 
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