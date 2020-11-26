<?php
  if (isset($_COOKIE["uid"])) {
    header('Location: index.php');
  }

  if($_POST['email']) {
    $email = $_POST['email'];
    //ini_set('display_errors', 0);
    $db = new mysqli('db', 'root', '331234', 'project');
    if ($db->connect_errno) {
      die("Failed to connect to database: (" . $db->connect_errno . ") " . $db->connect_error);
    }
    $result = $db->query("SELECT uid, name, password FROM user where email='{$_POST['email']}';");
    if(!$result->num_rows) {
      $msg = "User is not registered";
    } else {
      $row = $result->fetch_array();
      if ($row['password'] === "{$_POST['password']}") {
        setcookie("uid", "{$row['uid']}", time()+3600);
        header('Location: index.php');
      } else {
        $msg = "Unauthorized access";
      }
    }
    $result->free_result();
    $db->close();
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
<body>
  <div class="login card">
    <h1 id="login">Login</h1>
    <form action="" method="POST">
    <div class="input-group">
      <label for="email">Email</label>
      <input type="text" id="email" name="email" value="<?php echo $email; ?>" autofocus required>
    </div>
    <div class="input-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" id="loginButton">Log In</button>
    </form>
    <p class="error">
      <?php echo $msg; ?>
    </p>
  </div>
  <p>Do not have an account?</p>
  <a>Register</a>
</body>
</html>