<?php
  if (isset($_COOKIE["uid"])) {
    header('Location: index.php');
  }

  if($_POST['email']) {
    require_once "user.php";

    $email = $_POST['email'];
    $uid = getUid($email);
    if(!$uid) {
      $msg = "User is not registered";
    } else {
      if ( login($uid, $_POST['password']) ) {
        header('Location: index.php');
      } else {
        $msg = "Unauthorized access";
      }
    }
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Login</title>
  <link rel="stylesheet" href="css/global.css">
</head>
<style>

</style>
<body class="center-text align-viewport-center">
  <div class="center-box">
    <div class="card">
      <h1>Login</h1>
      <form action="" method="POST" class="center">
        <div class="input-group">
          <label for="email">Email</label>
          <input type="text" id="email" name="email" value="<?php echo $email; ?>" pattern="^.+@.+\.+.+"  autofocus required>
        </div>
        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button class="submit" type="submit" id="loginButton">Log In</button>
      </form>
      <div class="error">
        <?php echo $msg; ?>
      </div>
    </div>
    <p>Do not have an account?</p>
    <a href='register.php' class="button">Register</a>
  </div>
</body>
</html>