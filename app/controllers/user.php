<?php

require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/post.php';
require_once __DIR__ . '/../models/category.php';
require_once __DIR__ .  '/../core/view.php';
require_once __DIR__ .  '/../core/auth.php';

function user_index(){

    auth_require_user_type(['admin', 'boss']);

    $activeTab = $_GET['tab'] ?? '#user';
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $recordsPerPage = 7;
    $offset = ($currentPage - 1) * $recordsPerPage;

    // Count & fetch for the active tab (like before)
    $users = $admins = $bosses = [];
    $totalRecords = $totalPages = 1;

    switch ($activeTab) {
        case '#admin':
            $totalRecords = getAccountsCount('admin');
            $admins = getAccountsPaginated('admin', $recordsPerPage, $offset);
            break;
        case '#boss':
            $totalRecords = getAccountsCount('boss');
            $bosses = getAccountsPaginated('boss', $recordsPerPage, $offset);
            break;
        default:
            $totalRecords = getAccountsCount('user');
            $users = getAccountsPaginated('user', $recordsPerPage, $offset);
            break;
    }

    $totalPages = max(1, ceil($totalRecords / $recordsPerPage));

    $serialNumber = $offset + 1;

    $start = $offset + 1;
    $end = $offset + $recordsPerPage;
    $end = min($end, $totalRecords);

    if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
        // Render only the partial view and exit
        echo view('user/tabs', [
            'users' => $users,
            'admins' => $admins,
            'bosses' => $bosses,
            'activeTab' => $activeTab,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'recordsPerPage' => $recordsPerPage,
            'serialNumber' => $serialNumber,
            'start' => $start,
            'end' => $end,
        ],);
        exit;
    }


    echo view('user/user-list', ['title' => 'users', 'activeTab' => $activeTab], 'private');
}

function user_delete(){
    // 🔐 Check if user is logged in and is admin or boss
    auth_require_user_type(['admin', 'boss']);

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $userId = trim($_POST['delete-id']) ?? 0;
        $allPostsDeleted = true;

        $posts = getPostsbyUserId($userId);

        if (!empty($posts)) {
            foreach ($posts as $post) {
               
                $imagePath = 'assets/uploads/permanent/' . $post['post_image'];
               
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
               
                $deleted = deletePost($post['id']);
               
                if (!$deleted) {
                    $allPostsDeleted = false;
                }
            }
        }

        $deleteUser = deleteUser($userId);

        if($deleteUser && $allPostsDeleted) {
            header('location: ?c=user&a=index');
            exit;
        }

        // echo 'HEY !!!' . 'user id : '. $userId . '<br>';
        // echo '<pre>';
        // print_r($posts);
        // echo '</pre>';

    }

}


?>