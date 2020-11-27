<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Project</title>
  <script src="index.js" defer></script>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/index.css">
</head> 
<body>
    <?php
    ini_set('display_errors', 0);
    if (isset($_COOKIE["uid"])) {
        echo "<nav data-loggedin='true'></nav>";
    } else {
        echo "<nav data-loggedin='false'></nav>";
    }
    ?>
    <aside>
        <div class="space" data-space="Algorithm">Algorithm</div>
        <div class="space" data-space="Machine Learning">Machine Learning</div>
        <div class="space" data-space="System">System</div>
        <div class="space" data-space="JavaScript">JavaScript</div>
    </aside>
    <main>
    <?php
    require_once "db.php";
    if (isset($_COOKIE["uid"])) {
        $db = getdb();
        $result = $db->query("SELECT name FROM user WHERE uid={$_COOKIE["uid"]}");
        $row = $result->fetch_object();
        echo "<button id='ask'>Ask Question</button>";
        echo "
        <div class='question card'>
            <h4 class='user'>{$row->name}</h4>
            <p>What is your question?</p>
        </div>";
        $result->free_result();
        $db->close();
    }
    ?>
    <div id='questions'></div>
    </main>
    <script src="nav.js"></script>
</body>
</html>