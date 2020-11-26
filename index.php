<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Project</title>
  <script src="nav.js" defer></script>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/index.css">
</head> 
<body>
    <nav id="navbar"></nav>
    <aside>
        <div>Algorithm</div>
        <div>Machine Learning</div>
        <div>System</div>
        <div>JavaScript</div>
    </aside>
    <main>
    <?php
        $db = new mysqli('db', 'root', '331234', 'project');
        if ($db->connect_errno) {
          die("Failed to connect to database: (" . $db->connect_errno . ") " . $db->connect_error);
        }
        if (isset($_COOKIE["uid"])) {
            echo "
            <div class='question card'>
                <h4 class='user'>Bob</h4>
                <p>What is your question?</p>
            </div>";
            echo "<script>var loggedin = 1;</script>";
            $upvoted = array();
            $result = $db->query("SELECT qid FROM up WHERE uid={$_COOKIE["uid"]}");
            while ($row = $result->fetch_row()) {
                array_push($upvoted, $row[0]);
            }
        }

        $query = "SELECT q.qid, u.name, q.space, q.title, q.content, "
                ."DATE_FORMAT(q.time, '%d-%m-%Y') AS time, IFNULL(up.count, 0) AS up, IFNULL(ans.count, 0) AS ans "
                ."FROM question q JOIN user u ON q.creatorid = u.uid "
                ."LEFT OUTER JOIN (SELECT qid, COUNT(uid) AS count FROM up GROUP BY qid) up ON q.qid = up.qid "
                ."LEFT OUTER JOIN (SELECT qid, COUNT(aid) AS count FROM answer GROUP BY qid) ans ON q.qid = ans.qid;";
        $result = $db->query($query);
        while ($row = $result->fetch_array()) {
            if (in_array($row['qid'], $upvoted)) {
                $upvoteClass = ' upvoted';
            }
            echo "
            <div class='question card' qid='{$row['qid']}'>
                <div class='space'>{$row['space']}</div><br>
                Posted By <span>{$row['name']}</span> on <span>{$row['time']}</span>
                <h4>{$row['title']}</h4>
                <p>{$row['content']}</p>
                <span class='pl-1em{$upvoteClass}'>Upvote {$row['up']}</span><span class='pl-1em'>Answers {$row['ans']}</span>
            </div>
            ";
        }
        $result->free_result();
        $db->close();
    ?>
    </main>
</body>
</html>