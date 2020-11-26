<?php
  if (isset($_COOKIE["uid"])) {
    setcookie("uid", "", time()-1);
    header('Location: index.php');
  }
?>