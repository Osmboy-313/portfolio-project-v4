<?php 

function auth_require_login(){
    if(!isset($_SESSION['user'])){
        header('location: index.php?c=auth&a=index');
        exit;
    }
}

function auth_require_user_type($allowedTypes = []){
    // First check if user is logged in
    auth_require_login();
    
    // If no specific types required, just being logged in is enough
    if(empty($allowedTypes)){
        return true;
    }
    
    // Check if user's type is in allowed types
    $userType = $_SESSION['user']['user_type'] ?? '';
    
    if(!in_array($userType, $allowedTypes)){
        // Redirect to dashboard with error message
        $_SESSION['error'] = "Access denied. You don't have permission to view this page.";
        header('location: index.php?c=dashboard&a=index');
        exit;
    }
    
    return true;
}

function auth_is_logged_in(){
    return isset($_SESSION['user']);
}

function auth_get_user_type(){
    return $_SESSION['user']['user_type'] ?? null;
}

function auth_get_user_id(){
    return $_SESSION['user']['id'] ?? null;
}

?>