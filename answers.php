<?php
/*
GET:
    Take query parameter qid
    Return array of answers for qid in json
    Each answer has values:
        aid, qid, title, content, uid, time
    Result ordered by time ascending by default

POST:
    Take parameters qid, aid
    delete answer

    Take parameters qid, content
    add answer
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
            if (isset($_POST['qid']) && isset($_POST['aid'])) {
                delAnswer($db, $_POST['qid'], $_POST['aid']);
            } elseif (isset($_POST['qid']) && isset($_POST['content'])) {
                addAnswer($db, $_POST['qid'], $_POST['content']);
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
        } elseif ($e->getMessage() == 'not found') {
            http_response_code(404);
        } elseif ($e->getMessage() == 'forbidden') {
            http_response_code(403);
        } else {
            http_response_code(500);
        }
        echo $e->getMessage(), ' ', $e->getCode();
    }


    function getReq($db) {
        $answerArray = array();
        $order = 'time ASC, aid ASC';
        if (!isset($_GET['qid'])) {
            throw new Exception('bad request');
            return;
        }
        $query = "SELECT a.qid, a.aid, a.content, a.uid, u.name, "
                ."DATE_FORMAT(a.time, '%d-%m-%Y') AS time "
                ."FROM answer a JOIN user u ON u.uid=a.uid "
                ."WHERE a.qid='{$_GET['qid']}' "
                ."ORDER BY ".$order.";";

        $result = $db->query($query);
        if (!$result) {
            throw new Exception('query error');
        }
        if (!$result->num_rows) {
            throw new Exception('not found');
        }
        while ($answer = $result->fetch_object()) {
            array_push($answerArray, $answer);
        }
        $result->free_result();
        $json = json_encode($answerArray);
        header('Content-Type: application/json');
        echo $json;
    }

    function addAnswer($db, $qid, $content) {
        $uid = $_COOKIE['uid'];
        $time = date("Y-m-d");

        $query = "SELECT qid FROM question WHERE qid='{$qid}';";
        $result = $db->query($query);
        if (!$result) {
            throw new Exception('query error', $db->errno);
        }
        if (!$result->num_rows) {
            $result->free_result();
            throw new Exception('not found');
        }

        $query = "SELECT MAX(a.aid) AS max FROM answer a WHERE a.qid='{$qid}' GROUP BY a.qid";
        $result = $db->query($query);

        if (!$result->num_rows) {
            //no answer yet
            $result->free_result();
            $aid = 0;
        } else {
            $aid = $result->fetch_object()->max;
        }

        $aid++;

        $query = "INSERT INTO answer VALUE ('{$aid}', '{$qid}', '{$content}', '{$uid}', '{$time}');";
        $result = $db->query($query);
        if (!$result) {
            throw new Exception('query error', $db->errno);
        }
        echo $aid;
    }

    function delAnswer($db, $qid, $aid) {
        $uid = $_COOKIE['uid'];

        $query = "SELECT uid FROM answer WHERE qid='{$qid}' AND aid='{$aid}';";

        $result = $db->query($query);
        if (!$result) {
            throw new Exception('query error', $db->errno);
        }
        //check answer exists
        if (!$result->num_rows) {
            throw new Exception('not found');
        }
        //check user owns the answer
        if ($result->fetch_object()->uid != $uid) {
            throw new Exception('forbidden');
        }
        $query = "DELETE FROM answer WHERE qid='{$qid}' AND aid='{$aid}';";
        $result = $db->query($query);
        if (!$result) {
            throw new Exception('query error', $db->errno);
        }
    }

?>

