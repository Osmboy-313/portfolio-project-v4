<?php

require_once __DIR__ .  '/../models/code.php';
require_once __DIR__ .  '/../models/user.php';
require_once __DIR__ .  '/../core/view.php';
require_once __DIR__ .  '/../core/auth.php';

function auth_index()
{
    echo view('/auth/auth', ['title' => 'Authentication'], 'auth');
}

function auth_register()
{
    header('Content-Type: application/json');

    $response = [];
    $fetchedCodes = fetch_codes();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode(['failure' => 'Invalid input data']);
            return;
        }

        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $userType = trim($data['userType'] ?? '');
        $enteredCode = trim($data['code'] ?? '');
        $password = trim($data['password'] ?? '');
        $confirmPassword = trim($data['confirmPassword'] ?? '');

        if (empty($username)) $response['errors']['username'] = "Enter your username";
        if (empty($email)) $response['errors']['email'] = "Enter your email";
        if (empty($userType)) $response['errors']['userType'] = "Select your user type";
        if (empty($password)) $response['errors']['password'] = "Enter your password";
        if (empty($confirmPassword)) $response['errors']['confirmPassword'] = "Enter confirmed password";

        if (!empty($confirmPassword) && ($confirmPassword !== $password)) {
            $response['errors']['confirmPassword'] = "Passwords don't match!";
        }

        if (!empty($password) && ($confirmPassword === $password) && strlen($password) < 8) {
            $response['errors']['password'] = "Password must be at least 8 characters long";
            $response['errors']['confirmPassword'] = "Password must be at least 8 characters long";
        }

        if (!empty($userType) && $userType !== 'user') {
            if (empty($enteredCode)) {
                $response['errors']['code'] = "Enter the {$userType} code!";
            } else {
                $match = false;
                foreach ($fetchedCodes as $code) {
                    $match = $userType === 'admin' ? $code['admin_code'] === $enteredCode : $code['boss_code'] === $enteredCode;
                    if ($match) break;
                }
                if (!$match) {
                    $response['errors']['code'] = "Incorrect {$userType} code! Stop fighting a war you can't win!";
                }
            }
        }

        $usernameFromDb = check_user_exists(['column' => 'username', 'value' => $username]);
        $emailFromDB = check_user_exists(['column' => 'email', 'value' => $email]);

        if (!empty($username) && !empty($usernameFromDb)) {
            $response['errors']['username'] = "Username already exists";
        }
        if (!empty($email) && !empty($emailFromDB)) {
            $response['errors']['email'] = "Email already exists";
        }

        if (!isset($response['errors'])) {
            $addUser = addUser($username, $email, $userType, $password);

            if ($addUser) {
                $response['success'] = "Successfully Registered";
            } else {
                $response['failure'] = "Registration Failed. Please try again!";
            }
        }

        echo json_encode($response);
    }
}


function auth_login()
{
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode(['failure' => 'Invalid input data']);
            return;
        }

        $response = [];

        $email = trim($data['email'] ?? '');
        $userType = trim($data['userType'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($email)) $response['errors']['email'] = "Enter your email";
        if (empty($userType)) $response['errors']['userType'] = "Select your user type";
        if (empty($password)) $response['errors']['password'] = "Enter your password";

        if (!isset($response['errors'])) {
            $fetchedUser = get_user_pass($email, $userType);

            if (!empty($fetchedUser)) {
                $userPassword = $fetchedUser['password'];

                if (password_verify($password, $userPassword)) {
                    $response['success'] = "Successfully logged in";
                    $_SESSION['user'] = $fetchedUser;
                } else {
                    $response['failure'] = "Invalid Login Credentials";
                }
            } else {
                $response['failure'] = "Invalid Login Credentials";
            }
        }

        echo json_encode($response);
    }
}


function auth_logout(){
    if(isset($_SESSION['user'])){
        unset($_SESSION['user']);
        header('location: index.php');
    }
}

function auth_codes()
{
    header('Content-Type: application/json');

    $codes = fetch_codes();
    echo json_encode($codes);
}

function auth_checkUser(){

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $data = json_decode(file_get_contents('php://input'), true);

        
        $check = check_user_exists([ 'column' =>$data['column'], 'value' => $data['value']]);
        $exists = false;

        if(!empty($data['value'])){
            
            if(!empty($check)){
                $exists = true;
            }

        }
        
        echo json_encode(['exists' => $exists]);
    }
}
