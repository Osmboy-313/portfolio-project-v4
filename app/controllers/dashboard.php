<?php

require_once __DIR__ .  '/../core/view.php';
require_once __DIR__ .  '/../core/auth.php';

function dashboard_index(){
    // 🔐 Check if user is logged in
    auth_require_login();
    
    echo view('/dashboard/index', ['title' => 'Dashboard'], 'private');
}

?>