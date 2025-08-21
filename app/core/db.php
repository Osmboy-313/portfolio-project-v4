<?php

define("DB_HOST", "sql100.infinityfree.com");
define("DB_PASS", "yHsU1pdODr1");
define("DB_USERNAME", "if0_39697025");
define("DB_NAME", "if0_39697025_project");

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
