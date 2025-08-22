<?php

define("DB_HOST", "localhost");
define("DB_PASS", "");
define("DB_USERNAME", "root");
define("DB_NAME", "forum-project");

function db(){
    static $conn;

    if (!$conn) {

        $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASS, DB_NAME);

        if ($conn->connect_error) {
            die('connetion to DB failed' . $conn->connect_error);
        }
    }

    return $conn;
}
