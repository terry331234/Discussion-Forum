<?php
    require_once "db.php";

    //Return username if uid exist, else return 'not found'
    function getUsername($uid) {
        $db = getdb();
        $return = 'not found';
        $result = $db->query("SELECT name FROM user WHERE uid='{$uid}';");
        if ($result->num_rows) {
            $row = $result->fetch_object();
            $return = $row->name;
        }
        $result->free_result();
        $db->close();
        return $return;
    }

    //Return uid if email exist, else return 0
    function getUid($email) {
        $db = getdb();
        $return = 0;
        $result = $db->query("SELECT uid FROM user WHERE email='{$email}';");
        if ($result->num_rows) {
            $row = $result->fetch_object();
            $return = $row->uid;
        }
        $result->free_result();
        $db->close();
        return $return;
    }

    //Return 1 if login success, else return 0
    function login($uid, $password) {
        $db = getdb();
        $return = 0;
        $result = $db->query("SELECT password FROM user WHERE uid='{$uid}';");
        if ($result->num_rows) {
            $row = $result->fetch_object();
            if ($row->password === $password) {
                $return = 1;
                setcookie("uid", "{$uid}", time()+3600);
            }
        }
        $result->free_result();
        $db->close();
        return $return;
    }

    //Return 1 if creating user is successful, else return 0
    function addUser($name, $email, $password) {
        $db = getdb();
        $return = 0;
        $result = $db->query("INSERT INTO user VALUES ('0', '{$name}', '{$email}', '{$password}');");
        if ($result) {
            $return = 1;
        }
        $db->close();
        return $return;
    }

?>