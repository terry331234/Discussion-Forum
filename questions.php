<?php
/*
Take query parameters order, space, title, qid
Return array of questions in json
Each question has values:
    qid, name, space, title, content, time, upCount, ansCount, upvoted
Result ordered by time by default
*/
    require_once "db.php";

    if ($_GET) {
        $db = getdb();
        $upvoted = array();
        $questionArray = array();
        $uid = 0;
        $order = [' time DESC '];
        $filter = [];
        if ($_GET['order'] === 'up') {
            array_unshift($order, ' upCount DESC ');
        }
        if ($_GET['space']) {
            $filter[] = " q.space='{$_GET['space']}' ";
        }
        if ($_GET['title']) {
            $filter[] = " q.title LIKE '%{$_GET['title']}%' ";
        }
        if ($_GET['qid']) {
            $filter[] = " q.qid='{$_GET['qid']}' ";
        }
        if ($_COOKIE['uid']) {
            $uid = $_COOKIE['uid'];
            $result = $db->query("SELECT qid FROM up WHERE uid={$uid}");
            while ($row = $result->fetch_row()) {
                array_push($upvoted, $row[0]);
            }
        }
        $query = "SELECT q.qid, u.name, q.space, q.title, q.content, "
                ."DATE_FORMAT(q.time, '%d-%m-%Y') AS time, IFNULL(up.count, 0) AS upCount, IFNULL(ans.count, 0) AS ansCount "
                ."FROM question q JOIN user u ON q.creatorid = u.uid "
                ."LEFT OUTER JOIN (SELECT qid, COUNT(uid) AS count FROM up GROUP BY qid) up ON q.qid = up.qid "
                ."LEFT OUTER JOIN (SELECT qid, COUNT(aid) AS count FROM answer GROUP BY qid) ans ON q.qid = ans.qid ";
        if($filter) {
            $query .= "WHERE".implode('AND', $filter);
        }
        $query .= "ORDER BY".implode(',', $order).';';
        
        $result = $db->query($query);
        while ($question = $result->fetch_object()) {
            if ($uid) {
                $question->upvoted = in_array($question->qid, $upvoted);
            } else {
                $question->upvoted = false;
            }
            array_push($questionArray, $question);
        }
        $result->free_result();
        $db->close();
        $json = json_encode($questionArray);
        header('Content-Type: application/json');
        echo $json;
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Please specify parameters";
        die();
    }
?>

