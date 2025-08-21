<?php

require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/post.php';
require_once __DIR__ . '/../models/category.php';
require_once __DIR__ .  '/../core/view.php';
require_once __DIR__ .  '/../core/auth.php';

function profile_myProfile(){

    echo view('profile/my-profile', ['title' => 'My Profile'], 'private');
}

function profile_get(){

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $id = $_SESSION['user']['id'];
        $user = getUserById($id);
        echo json_encode($user);
    }
    
}

// function profile_update(){

//     if($_SERVER['REQUEST_METHOD'] === 'POST'){
//         $data = json_decode(file_get_contents('php://input'), true);
//         $response = [];

//         $id = $data['id'] ?? 0;
//         $updatedUser = '';
//         $updateFields = [];

//         if (!empty($data['username']))  $updateFields['username'] = $data['username'];
//         if (!empty($data['email']))     $updateFields['email'] = $data['email'];
//         if (!empty($data['userType'])) $updateFields['user_type'] = $data['userType'];
//         if (!empty($data['password']))  $updateFields['password'] = $data['password'];
//         if (!empty($data['profile_picture'])) $updateFields['profile_picture'] = $data['profile_picture'];

//         $success = updateUser($id, $updateFields);

//         if($success){
//             $response['success'] = 'Successfully Updated the User Type';
//             $updatedUser = getUserById($id);
//             $_SESSION['user'] = $updatedUser;
//         }
//         else{
//             $response['failure'] = 'Failed to Updat the User Type';
//         }

//         echo json_encode($response);
//     }

// }


function profile_update() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $response = [];

        $id = $data['id'] ?? 0;
        $updatedUser = '';
        $updateFields = [];

        $user = getUserById($id);

        if (!$user) {
            $response['failure'] = 'User not found';
            echo json_encode($response);
            return;
        }

        if (!empty($data['password'])) {
            $currentPassword = $data['currentPassword'] ?? '';

            if (!password_verify($currentPassword, $user['password'])) {
                $response['errors']['currentPassword'] = 'Current password is incorrect';
                echo json_encode($response);
                return;
            }

            $updateFields['password'] = $data['password'];
        }

        if (!empty($data['username']))  $updateFields['username'] = $data['username'];
        if (!empty($data['email']))     $updateFields['email'] = $data['email'];
        if (!empty($data['userType']))  $updateFields['user_type'] = $data['userType'];
        if (!empty($data['profile_picture'])) $updateFields['profile_picture'] = $data['profile_picture'];

        $success = updateUser($id, $updateFields);

        if ($success) {
            $response['success'] = 'Successfully updated user';
            $updatedUser = getUserById($id);
            $_SESSION['user'] = $updatedUser;
        } else {
            $response['failure'] = 'Failed to update user';
        }

        echo json_encode($response);
    }
}




function profile_doesUserExists(){

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $data = json_decode(file_get_contents('php://input'), true);
        
        $id = $data['id'] ?? 0;
        $column = $data['column'] ??'';
        $value = $data['value'] ??'';

        $userExists = doesUserExists($column, $value, $id);
        $valid = true;

        if(!empty($userExists)){
            $valid = false;
        }

        echo json_encode(['valid' => $valid]);

    }

}

function profile_preview(){
    // 🔐 Check if user is logged in
    auth_require_login();

    $userId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    $recordsPerPage = 4;

    $currentPage = isset($_GET['page']) ? (int)$_GET['page']: 1;
    $totalRecords = countAllPaginatedPosts($userId); 
    $totalPages   = (int)ceil($totalRecords / $recordsPerPage);

    if ($currentPage < 1)        $currentPage = 1;
    elseif ($currentPage > $totalPages) $currentPage = $totalPages;

    $offset = ($currentPage - 1) * $recordsPerPage;
    
    $userProfile = getUserWithPosts($userId, $recordsPerPage, $offset);
    $user = $userProfile['user'];
    $posts = $userProfile['posts'];

    // $posts = getAllPosts();

    $start = $offset + 1;
    $end = $offset + $recordsPerPage;
    $end = min($end, $totalRecords);

    $modals = view('components/modals');

    echo view('profile/profile-preview', [
        'title' => 'User Profile',
        'user' => $user,
        'posts' => $posts,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'totalRecords' => $totalRecords,
        'start' => $start,
        'end' => $end,
        'modals' => $modals,
        ],
        'private');

}

?>