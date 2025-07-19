<?php

$localhost = "localhost";
$dbName = "assignment";
$userName = "root";
$password = "";

$connection = mysqli_connect($localhost, $userName, $password, $dbName);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

?>