<?php

function view(string $path, array $var = [], ?string $layout = null ){

    extract($var, EXTR_SKIP);

    ob_start();
    require __DIR__ . "/../views/{$path}.php";
    $content = ob_get_clean();

    if($layout === null){
        return $content;
    }

    ob_start();
    require __DIR__ . "/../views/layouts/{$layout}.php";
    return ob_get_clean();

}

?>