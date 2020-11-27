<?php
/*
GET:
    Take query parameters order, space, title, qid
    Return array of questions in json
    Each question has values:
        qid, name, space, title, content, time, upCount, ansCount, upvoted
    Result ordered by time by default

POST:
    Take parameters uid, qid, action('add'/'del')
    add/delete upvote from uid for qid
    Return 'ok' if success, else return 'error'
*/
    require_once "db.php";
    //ini_set('display_errors', 0);

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
    } elseif ($_POST) {
        if ($_POST['uid'] && $_POST['qid'] && $_POST['action']) {
            $uid = $_POST['uid'];
            $qid = $_POST['qid'];

            if ($_POST['action'] == 'add') {
                $query = "INSERT INTO up VALUE ('{$uid}', '{$qid}');";
            } elseif ($_POST['action'] == 'del') {
                $query = "DELETE FROM up WHERE uid='{$uid}' AND qid='{$qid}';";
            } else {
                header("HTTP/1.0 404 Not Found");
                echo "Wrong parameters";
                die();
            }
            $db = getdb();
            $return = 'not found';
            $result = $db->query($query);
            if ($result) {
                header('Content-Type: text/plain');
                echo 'ok';
            } else {
                echo 'error';
            }
            $db->close();
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Missing parameters";
        }
    } 
    else {
        header("HTTP/1.0 404 Not Found");
        echo "Please specify parameters";
    }
?>

