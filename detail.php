<?php
  if (!isset($_GET['qid'])) {
      http_response_code(400);
      die();
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Ask!</title>
  <link rel="stylesheet" href="css/global.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="js/detail.js" type="module"></script>
  <script src="js/answers.js" type="module"></script>
  <link rel="shortcut icon" href="favicon.ico">
</head>
<style>
#back {
  display: inline-block;
}

</style>
<?php
    ini_set('display_errors', 0);
    if (isset($_COOKIE["uid"])) {
        require_once "user.php";
        $name = getUsername($_COOKIE["uid"]);
        echo "<body data-loggedin='true' data-uid='{$_COOKIE["uid"]}' data-name='{$name}'>";
    } else {
        echo "<body data-loggedin='false'>";
    }
?>
  <a id="back" class="button" href='index.php'>back</a>
  <main class='questions'>
    <?php
      echo "<div id=question data-qid={$_GET['qid']}";
      if (isset($_COOKIE["uid"])) {
        echo " data-uid={$_COOKIE["uid"]}";
      }
      echo "></div>";
    ?>
  </main>
</body>
</html>