<?php

include "connection.php";

if (isset($_POST["register"])) {
  $username = $_POST["username"];
  $email = $_POST["email"];
  $password = $_POST["password"];
  $phone = $_POST["phone"];
  $address = $_POST["address"];

  // image 
  $avatar = $_FILES["avatar"]["name"];
  $path = "../images/";
  $image = $path . $avatar;

  $copy = copy($_FILES['avatar']['tmp_name'], $image);

  $query = "INSERT INTO recipe (username, email, password, phone, address, avatar)
  VALUES ('$username', '$email', '$password', '$phone', '$address', '$image')";

  $result = mysqli_query($connection, $query);

  if ($result) {
    echo "Success";
  } else {
    echo "Failed";
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <h1>Recipe Form</h1>

  <form action="register.php" method="post" enctype="multipart/form-data">
    <label>Name:</label>
    <input type="text" name="Name" placeholder="Enter your name...">

    <br><br>

    <label>Description:</label>
    <input type="text" name="description" placeholder="Enter your description...">

    <br><br>

    <label>Instructions:</label>
    <input type="text" name="instructions" placeholder="Enter your instructions...">

    <br><br>

    <label>Prep time minutes:</label>
    <input type="number" name="prep_time_minutes" placeholder="Enter prep time minutes...">

    <br><br>

    <label>Cooking time minutes:</label>
    <input type="number" name="cook_time_minutes" placeholder="Enter cooking time minutes...">

    <br><br>

    <label>Avatar:</label>
    <input type="file" name="avatar" accept="image/*" onchange="loadFile(event)">
    <img id="uploadAvatar" width="200" height="200">

    <script>
      const loadFile = function (e) {
        const avatar = document.getElementById("uploadAvatar")
        avatar.src = URL.createObjectURL(e.target.files[0]);
      }
    </script>

    <br><br>

    <input type="reset" name="clear" value="Clear">
    <input type="submit" name="register" value="Register">
  </form>
</body>

</html>