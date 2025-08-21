<?php

function dispatch(string $c, string $a){

    $allow = ['home', 'auth', 'post', 'category', 'code', 'user' , 'admin', 'dashboard', 'profile'];

    if(!in_array($c, $allow, true)){
        http_response_code(404);
        echo '404';
        return;
    }

    require_once __DIR__ . "/../controllers/{$c}.php";

    $controller_function = "{$c}_{$a}";

    if($controller_function){
        $controller_function();
    }
    else{
        http_response_code(404);
        echo 'Action not found !';
    }

}

?>

