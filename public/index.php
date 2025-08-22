<?php 

$projectFolder = basename(dirname(__DIR__));
session_name($projectFolder . '_session');
session_start();

require_once __DIR__ . '/../app/core/bootstrap.php';

$c = $_GET['c'] ?? 'home';
$a = $_GET['a'] ?? 'index';

unsetTempSession($c,$a);
dispatch($c,$a);

?>