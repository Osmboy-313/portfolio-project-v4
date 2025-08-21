<?php 

// Start session for authentication
// session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('BASEPATH', dirname(__DIR__ ,1));
define('VIEWPATH', BASEPATH . '/views');

require_once __DIR__ . '/router.php';
require_once __DIR__ . '/view.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function d(...$var){
    echo "<pre>";
    var_dump(...$var);
    echo "<pre>";
}

?>