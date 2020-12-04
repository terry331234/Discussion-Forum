<?php
  if (isset($_COOKIE["uid"])) {
    header('Location: index.php');
  }

  if($_POST['email']) {
    require_once "user.php";
    
    $email = $_POST['email'];
    $uid = getUid($email);
    if($uid) {
      $msg = "Duplicated user's email address";
    } else {
      if (addUser($_POST['name'], $email, $_POST['password'])) {
        header('Location: index.php');
      } else {
        die("Failed to add new user!");
      }
    }
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Ask! - Register</title>
  <link rel="stylesheet" href="css/global.css">
</head>
<style>

</style>
<body class="center-text align-viewport-center">
  <div class="center-box card">
    <h1>Create an account</h1>
    <form action="" method="POST" class="center">
      <div class="input-group">
        <label for="email">Email</label>
        <input type="text" name="email" pattern="^.+@.+\.+.+" maxlength=50 autofocus required>
      </div>
      <div class="input-group">
        <label for="name">Name</label>
        <input type="text" name="name" maxlength=50 required>
      </div>
      <div class="input-group">
        <label for="password">Password</label>
        <input id='password' type="password" name="password" maxlength=30 required>
      </div>
      <div class="input-group">
        <label for="confirmPassword">Confirmation</label>
        <input id='confirm' type="password" maxlength=30 required>
      </div>
      <button class="submit" type="submit">Register</button>
    </form>
    <div class="error">
      <?php echo $msg; ?>
    </div>
  </div>
  <script>
    var password = document.getElementById('password');
    var confirm = document.getElementById('confirm');

    password.oninput = checkPassword;
    confirm.oninput = checkPassword;

    function checkPassword() {
        if ((password.value != confirm.value)) {
            if (confirm.checkValidity()) {
                confirm.setCustomValidity('Inputted passwords do not match!');
            }
        } else {
            confirm.setCustomValidity('');
        }
    }
  </script>
</body>
</html>