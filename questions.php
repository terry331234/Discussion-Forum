<?php
/*
GET:
    Take query parameters order(required), space, title, qid
    Return array of questions in json
    Each question has values:
        qid, name, space, title, content, time, upCount, ansCount, upvoted
    Result ordered by time descendingly by default

POST:
    Take parameters qid, action('add'/'del')
    add/delete current user's upvote for qid
*/
    require_once "db.php";
    //ini_set('display_errors', 0);
    function exceptions_error_handler($severity, $message, $filename, $lineno) {
        throw new ErrorException($message, 0, $severity, $filename, $lineno);
    }
    
    set_error_handler('exceptions_error_handler');
    try {
        if ($_GET) {  
            $db = getdb();
            getReq($db);
            $db->close();
        }
        elseif ($_POST) {
            if (!isset($_COOKIE['uid'])) {
                http_response_code(401);
                die();
            }
            $db = getdb();
            if (isset($_POST['qid']) && isset($_POST['action'])) {
                    upvote($db, $_POST['qid'], $_POST['action']);
            } elseif (isset($_POST['space']) && isset($_POST['title']) && isset($_POST['content'])) {
                    addQuestion($db, $_POST['space'], $_POST['title'], $_POST['content']);
            } elseif (isset($_POST['qid'])) {
                    delQuestion($db, $_POST['qid']);
            } else {
                http_response_code(400);
            }
            $db->close();
        }
        else {
            http_response_code(400);
        }
    } catch (ErrorException $e) {
        http_response_code(500);
        echo "error: ".$e->getMessage(), ' '.$e->getFile(), ':'.$e->getLine(), "\n";
    } catch (Exception $e) {
        if ($e->getMessage() == 'bad request') {
            http_response_code(400);
        } elseif ($e->getMessage() == 'question does not exist') {
            http_response_code(404);
        } elseif ($e->getMessage() == 'forbidden') {
            http_response_code(403);
        } else {
            http_response_code(500);
        }
        echo $e->getMessage(), ' ', $e->getCode();
    }


    function getReq($db) {
        $upvoted = array();
        $questionArray = array();
        $uid = 0;
        $order = [' time DESC '];
        $filter = [];
        if ( !( isset($_GET['order']) || isset($_GET['qid']) )) {
            throw new Exception('bad request');
        }
        if (isset($_GET['order']) && $_GET['order'] === 'up') {
            array_unshift($order, ' upCount DESC ');
        }
        if (isset($_GET['space'])) {
            $filter[] = " q.space='{$_GET['space']}' ";
        }
        if (isset($_GET['title'])) {
            $filter[] = " q.title LIKE '%{$_GET['title']}%' ";
        }
        if (isset($_GET['qid'])) {
            $filter[] = " q.qid='{$_GET['qid']}' ";
        }
        if (isset($_COOKIE['uid'])) {
            $uid = $_COOKIE['uid'];
            $result = $db->query("SELECT qid FROM up WHERE uid={$uid}");
            while ($row = $result->fetch_row()) {
                array_push($upvoted, $row[0]);
            }
        }
        $query = "SELECT q.qid, u.name, q.creatorid, q.space, q.title, q.content, "
                ."DATE_FORMAT(q.time, '%d-%m-%Y') AS time, IFNULL(up.count, 0) AS upCount, IFNULL(ans.count, 0) AS ansCount "
                ."FROM question q JOIN user u ON q.creatorid = u.uid "
                ."LEFT OUTER JOIN (SELECT qid, COUNT(uid) AS count FROM up GROUP BY qid) up ON q.qid = up.qid "
                ."LEFT OUTER JOIN (SELECT qid, COUNT(aid) AS count FROM answer GROUP BY qid) ans ON q.qid = ans.qid ";
        if($filter) {
            $query .= "WHERE".implode('AND', $filter);
        }
        $query .= "ORDER BY".implode(',', $order).';';
        
        $result = $db->query($query);
        if (!$result) {
            throw new Exception('query error');
        }
        while ($question = $result->fetch_object()) {
            if ($uid) {
                $question->upvoted = in_array($question->qid, $upvoted);
            } else {
                $question->upvoted = false;
            }
            array_push($questionArray, $question);
        }
        $result->free_result();
        $json = json_encode($questionArray);
        header('Content-Type: application/json');
        echo $json;
    }

    function upvote($db, $qid, $action) {
        $uid = $_COOKIE['uid'];
        if ($action == 'add') {
            $query = "INSERT INTO up VALUE ('{$qid}', '{$uid}');";
        } elseif ($action == 'del') {
            $query = "DELETE FROM up WHERE uid='{$uid}' AND qid='{$qid}';";
        } else {
            throw new Exception('bad request');
        }
        $result = $db->query($query);
        if (!$result) {
            throw new Exception('query error', $db->errno);
        }
    }

    function addQuestion($db, $space, $title, $content) {
        $uid = $_COOKIE['uid'];

        $time = date("Y-m-d");
        $query = "INSERT INTO question VALUE ('0', '{$space}', '{$title}', '{$content}', '{$time}', '{$uid}');";
        $result = $db->query($query);
        if (!$result) {
            throw new Exception('query error', $db->errno);
        }
        echo $db->insert_id;
    }

    function delQuestion($db, $qid) {
        $uid = $_COOKIE['uid'];

        $query = "SELECT creatorid FROM question WHERE qid='{$qid}';";

        $result = $db->query($query);
        if (!$result) {
            throw new Exception('query error', $db->errno);
        }
        //check question exists
        if (!$result->num_rows) {
            throw new Exception('question does not exist');
        }
        //check user owns the question
        if ($result->fetch_object()->creatorid != $uid) {
            throw new Exception('forbidden');
        }
        $query = "DELETE FROM question WHERE qid='{$qid}';";
        $result = $db->query($query);
        if (!$result) {
            throw new Exception('query error', $db->errno);
        }
    }

?>

