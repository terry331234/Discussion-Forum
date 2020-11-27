<?php
    function getdb() {
        $db = new mysqli('db', 'root', '331234', 'project');
        if ($db->connect_errno) {
            die("Failed to connect to database: (" . $db->connect_errno . ") " . $db->connect_error);
        }
        return $db;
    }
?>
