<?php

session_start();
session_destroy();

echo "
  <script>
    window.alert('Logout Success.')
    window.location='admin-sign-in.php'
  </script>
";

?>