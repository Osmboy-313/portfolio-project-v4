<?php

function url($c,$a,$extra = []){

    $query = http_build_query(array_merge(['c' => $c, 'a' => $a ], $extra));

    return "index.php?{$query}";

}

function active( string $c , ?string $a = null){

    $controller = $_GET['c'] ?? 'home';
    $action = $_GET['a'] ?? 'index';

    return ($c === $controller && ($a === null || $a === $action)) ? 'active' : '';

}

function unsetTempSession($c, $a){

    // keep temp-upload alive on add & edit
    if ($c === 'post' && in_array($a, ['add','edit'], true)) {
        return;
    }
    // otherwise clear it
    if (isset($_SESSION['temp-upload'])) {
        unlink($_SESSION['temp-upload']['file-temp-path']);
        unset($_SESSION['temp-upload']);
    }
}

function paginationDesign($currentPage, $totalPages){
    $pages = [];

    if ($totalPages <= 7) {
        for ($i = 1; $i <= $totalPages; $i++) {
            $pages[] = $i;
        }
    } else {
        if ($currentPage <= 3) {
            $pages = [1, 2, 3, 4, 5, '...', $totalPages];
        } else if ($currentPage >= $totalPages - 3) {
            $pages = [1, '...', $totalPages - 4, $totalPages - 3, $totalPages - 2, $totalPages - 1, $totalPages];
        } else {
            $pages = [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $totalPages];
        }
    }

    return $pages;
}


function ip_in_range($ip, $range) {
    if (strpos($range, '/') === false) {
        return $ip === $range;
    }
    
    list($subnet, $bits) = explode('/', $range);
    $ip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    $subnet &= $mask;
    
    return ($ip & $mask) == $subnet;
}


?>