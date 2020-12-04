<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Ask!</title>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
  <script src="js/index.js" type='module'></script>
  <script src="js/answers.js" type='module'></script>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/index.css">
</head> 
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
    <nav></nav>
    <input id='showSpace' class='hidden' type="checkbox">
    <label for='showSpace' id='showSpaceBtn' role='button'>Show Spaces</label>
    <aside>
        <div data-space="Algorithm">Algorithm</div>
        <div data-space="Machine Learning">Machine Learning</div>
        <div data-space="System">System</div>
        <div data-space="Javascript">Javascript</div>
    </aside>
    <main class='questions'>
    <?php
    if (isset($_COOKIE["uid"])) {
        echo "<a class='float-r' role='button' href='ask.php'>Ask Question</a>";
        echo "
        <div class='question card'>
            <h4 id='user'>{$name}</h4>
            <p class='info'>What is your question?</p>
        </div>";
    }
    ?>
    <div id='questions'></div>
    </main>
    <script src="js/nav.js"></script>
</body>
</html>