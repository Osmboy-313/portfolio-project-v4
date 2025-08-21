<?php

require_once __DIR__ . '/../models/post.php';
require_once __DIR__ . '/../models/category.php';
require_once __DIR__ .  '/../core/view.php';
require_once __DIR__ .  '/../core/auth.php';

define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024);        // 10MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

function post_index(){
    auth_require_login();

    $recordsPerPage = 3;

    $currentPage = isset($_GET['page']) ? (int)$_GET['page']: 1;
    $totalRecords = countUserPosts(); 
    $totalPages   = (int)ceil($totalRecords / $recordsPerPage);

    if ($currentPage < 1)        $currentPage = 1;
    elseif ($currentPage > $totalPages) $currentPage = $totalPages;

    $offset = ($currentPage - 1) * $recordsPerPage;
    $posts = getUserPaginatedPosts($recordsPerPage, $offset);

    $start = $offset + 1;
    $end = $offset + $recordsPerPage;
    $end = min($end, $totalRecords);

    $modal = view('/components/modals');
    echo view('/posts/my', [
        'title' => 'My Posts',
        'modals' => $modal,
        'posts' => $posts,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'totalRecords' => $totalRecords,
        'start' => $start,
        'end' => $end,
        ],
        'private');
}

function post_all(){

    auth_require_user_type(['admin', 'boss']);

    $recordsPerPage = 3;

    $currentPage = isset($_GET['page']) ? (int)$_GET['page']: 1;
    $totalRecords = countAllPosts(); 
    $totalPages   = (int)ceil($totalRecords / $recordsPerPage);

    if ($currentPage < 1)        $currentPage = 1;
    elseif ($currentPage > $totalPages) $currentPage = $totalPages;

    $offset = ($currentPage - 1) * $recordsPerPage;
    $posts = getAllPaginatedPosts($recordsPerPage, $offset);
    // $posts = getAllPosts();

    $start = $offset + 1;
    $end = $offset + $recordsPerPage;
    $end = min($end, $totalRecords);

    $modal = view('/components/modals');
    echo view('/posts/all', [
        'title' => 'All Posts',
        'modals' => $modal,
        'posts' => $posts,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'totalRecords' => $totalRecords,
        'start' => $start,
        'end' => $end,
        ],
        'private');
}


// ============================================ New clean up funciton with PRG technique ===============================================

function post_add()
{

    auth_require_login();

    $errors = $_SESSION['errors'] ?? [];
    $oldValues = $_SESSION['old-form'] ?? [];
    $status = $_SESSION['status'] ?? [];

    unset($_SESSION['errors'], $_SESSION['old-form'], $_SESSION['status']);

    $uploadsPermDir = __DIR__ . '/../../public/assets/uploads/permanent/';
    $uploadsTempDir = __DIR__ . '/../../public/assets/uploads/temp/';

    if (!is_dir($uploadsPermDir)) mkdir($uploadsPermDir, 0755, true);
    if (!is_dir($uploadsTempDir)) mkdir($uploadsTempDir, 0755, true);


    // Get Request ! Render Form with error, success or anything 

    $title = 'Add Post';
    $categories = getAllGlobalCategories(); // Updated to use global categories

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        if (empty($oldValues) && isset($_SESSION['temp-upload'])) {
            unlink($_SESSION['temp-upload']['file-temp-path']);
            unset($_SESSION['temp-upload']);
        }

        echo view('/posts/add', compact('title', 'categories', 'errors', 'oldValues', 'status'), 'private');

        return;
    }

    $title = htmlspecialchars($_POST['title'] ?? '');
    $tags = htmlspecialchars($_POST['tags'] ?? '');
    $description = htmlspecialchars($_POST['description'] ?? '');
    $category = htmlspecialchars($_POST['category'] ?? '');

    $image_O_Name = $_SESSION['temp-upload']['original-name'] ?? '';
    $image_S_Name = $_SESSION['temp-upload']['storage-name'] ?? '';
    $didImgUpload = $_SESSION['temp-upload']['didImgUpload'] ?? false;

    $uploadDetails = validateImage();

    if (isset($uploadDetails['error'])) {

        if ($uploadDetails['error'] && !isset($_SESSION['temp-upload'])) {
            $errors['image'] = $uploadDetails['error'];
        }
    } else if (!empty($uploadDetails)) {

        $fileTempPath = $uploadsTempDir . $uploadDetails['storage-name'];
        if (!move_uploaded_file($uploadDetails['php-temp-dir'], $fileTempPath)) {
            $errors['image'] = 'Failed to store File temporarily';
        } else {

            $_SESSION['temp-upload'] = [
                'original-name' => $uploadDetails['original-name'],
                'storage-name' => $uploadDetails['storage-name'],
                'file-temp-path' => $fileTempPath,
                'didImgUpload' => true,
            ];
            $image_O_Name = $uploadDetails['original-name'];
            $image_S_Name = $uploadDetails['storage-name'];
            $didImgUpload = true;
        }
    }

    if (empty($title)) $errors['title'] = 'Enter Post Title';
    if (empty($tags)) $errors['tags'] = 'Enter Post Tags';
    if (empty($description)) $errors['description'] = 'Enter Post Description';
    if (empty($category)) $errors['category'] = 'Select Post category';

    $oldValues = compact('title', 'tags', 'description', 'category', 'didImgUpload', 'image_O_Name');

    // Post Request ! Redirect to same page with errors and exit !

    if (!empty($errors)) {

        $_SESSION['errors'] = $errors;
        $_SESSION['old-form'] = $oldValues;
        header('location: ?c=post&a=add');
        exit;
    }

    $tempUpload = $_SESSION['temp-upload'];
    $src = $oldValues['file-temp-path'] ?? $tempUpload['file-temp-path'];
    $dest = $uploadsPermDir . ($oldValues['storage-name'] ?? $tempUpload['storage-name']);

    // Another Post Request ! redirect to same page with error if no file is moved from src to destination and then exit !

    if (!file_exists($src) || !rename($src, $dest)) {

        $errors['image'] = '';
        $_SESSION['errors'] = $errors;
        $_SESSION['old-form'] = $oldValues;
        header('location: ?c=post&a=add');
        exit;
    }

    $addPost = addPost($title, $tags, $description, $category, $image_O_Name, $image_S_Name);
    unset($_SESSION['temp-upload']);

    if ($addPost) {
        $_SESSION['status'] = ['alert--success' => 'Successfully Uploaded The Post !'];
        header('Location: ?c=post&a=add');
        exit;
    } else {
        $_SESSION['status'] = ['alert--failure' => 'Failed To Upload Post !'];
        header('Location: ?c=post&a=add');
        exit;
    }
}

function post_edit()
{
    // 🔐 Check if user is logged in
    auth_require_login();

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $Userpost = getPostById($id);

    $post = [
        'id' => $Userpost['id'],
        'title' => $Userpost['post_title'],
        'tags' => $Userpost['post_tags'],
        'description' => $Userpost['post_description'],
        'category' => $Userpost['post_category'],
        'image_S_name' => $Userpost['post_image'],
        'image_O_name' => $Userpost['post_img_original_name'],
        'user' => $Userpost['post_user'],
        'created' => $Userpost['created_at'],
        'updated' => $Userpost['updated_at'],
        'category_name' => $Userpost['category_name'],
    ];

    $errors = $_SESSION['errors'] ?? [];
    $oldValues = $_SESSION['old-form'] ?? $post;
    $status = $_SESSION['status'] ?? [];

    unset($_SESSION['errors'], $_SESSION['old-form'], $_SESSION['status']);

    $uploadsPermDir = __DIR__ . '/../../public/assets/uploads/permanent/';
    $uploadsTempDir = __DIR__ . '/../../public/assets/uploads/temp/';

    if (!is_dir($uploadsPermDir)) mkdir($uploadsPermDir, 0755, true);
    if (!is_dir($uploadsTempDir)) mkdir($uploadsTempDir, 0755, true);

    // Get Request ! Render Form with error, success or anything 

    $title = 'Edit Post';
    $categories = getAllGlobalCategories(); // Updated to use global categories

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        if ($oldValues === $post && isset($_SESSION['temp-upload'])) {
            unlink($_SESSION['temp-upload']['file-temp-path']);
            unset($_SESSION['temp-upload']);
        }

        echo view('/posts/edit', compact('title', 'categories', 'errors', 'oldValues', 'status'), 'private');

        return;
    }

    $title = htmlspecialchars($_POST['title'] ?? '');
    $tags = htmlspecialchars($_POST['tags'] ?? '');
    $description = htmlspecialchars($_POST['description'] ?? '');
    $category = htmlspecialchars($_POST['category'] ?? '');

    $image_O_name = $_SESSION['temp-upload']['original-name'] ?? $post['image_O_name'];
    $image_S_name = $_SESSION['temp-upload']['storage-name'] ?? $post['image_S_name'];
    $didImgUpload = $_SESSION['temp-upload']['storage-name'] ?? false;

    $uploadDetails = validateImage();

    if (isset($uploadDetails['error'])) {

        if ($uploadDetails['error'] && empty($image_O_name) && !isset($_SESSION['temp-upload'])) {
            $errors['image'] = $uploadDetails['error'];
        }

    } 
    else if (!empty($uploadDetails)) {

        $fileTempPath = $uploadsTempDir . $uploadDetails['storage-name'];

        if (move_uploaded_file($uploadDetails['php-temp-dir'], $fileTempPath)) {

            $_SESSION['temp-upload'] = [
                'original-name' => $uploadDetails['original-name'],
                'storage-name' => $uploadDetails['storage-name'],
                'file-temp-path' => $fileTempPath,
                'didImgUpload' => true,
            ];
            $image_O_name = $uploadDetails['original-name'];
            $image_S_name = $uploadDetails['storage-name'];
            $didImgUpload = true;
        }
        else {
            $errors['image'] = 'Failed to store File temporarily';
        }
    }


    if (empty($title)) $errors['title'] = 'Enter Post Title';
    if (empty($tags)) $errors['tags'] = 'Enter Post Tags';
    if (empty($description)) $errors['description'] = 'Enter Post Description';
    if (empty($category)) $errors['category'] = 'Select Post category';

    $noChange =
        $title          === $post['title']
        && $tags        === $post['tags']
        && $description === $post['description']
        && $category    === (string)$post['category']
        && ! $didImgUpload;

    if ($noChange) {

        $_SESSION['status'] = ['alert--warning' => 'You didnt change anything.'];

        $_SESSION['old-form'] = [
            'id'           => $id,
            'title'        => $post['title'],
            'tags'         => $post['tags'],
            'description'  => $post['description'],
            'category'     => $post['category'],
            'image_O_name' => $post['image_O_name'],
            'didImgUpload' => false,
        ];

        header("Location: ?c=post&a=edit&id={$id}");
        exit;
    }

    $oldValues = compact('title', 'tags', 'description', 'category', 'didImgUpload', 'image_O_name', 'image_S_name');

    // Post Request ! Redirect to same page with errors and exit !

    if (!empty($errors)) {

        $_SESSION['errors'] = $errors;
        $_SESSION['old-form'] = $oldValues + ['id' => $id];
        header("Location: ?c=post&a=edit&id={$id}");
        exit;
    } 

    if (isset($_SESSION['temp-upload'])) {
    
        $tempUpload = $_SESSION['temp-upload'];
        $src = $tempUpload['file-temp-path'];
        $dest = $uploadsPermDir . $tempUpload['storage-name'];
        $existingFile = $uploadsPermDir . $post['image_S_name'];
    
        if (!file_exists($src) || !rename($src, $dest)) {
            $errors['image']    = 'Failed to move uploaded file';
            $_SESSION['errors'] = $errors;
            $_SESSION['old-form'] = $oldValues + ['id' => $id];
            header("Location: ?c=post&a=edit&id={$id}");
            exit;
        }
    
        // success: clean up temp, delete old
        unset($_SESSION['temp-upload']);
        if (file_exists($existingFile)) {
            unlink($existingFile);
        }

    }

    $updatePost = updatePost($id ,$title, $tags, $description, $category, $image_O_name, $image_S_name);

    if($updatePost){
        $_SESSION['status'] = ['alert--success' => 'Post has been Successfully edited !'];
        header("Location: ?c=post&a=edit&id={$id}");
        exit;
    } else {
        $_SESSION['status'] = ['alert--failure' => 'Failed To Upload Post !'];
        header('Location: ?c=post&a=add');
        exit;
    }

}

// function post_delete(){

//     if($_SERVER['REQUEST_METHOD'] === 'POST' ){

//         $id = trim($_POST['id']);
//         $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '?c=post&a=index';

//         $post = getPostById($id);
//         $filename = 'assets/uploads/permanent/'. $post['post_image'];

//         // $deletePost = true;

//         if (file_exists($filename)) {
//             unlink($filename);

//             $deletePost = deletePost( $id );

//             if($deletePost){
//                 if (preg_match('/^\?c=post&a=(index|all|userPosts)(&page=\d+)?$/', $redirect)) {
//                     header("Location: $redirect");
//                 }                
//             }

//         }

//     }

// }

function post_delete() {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id = trim($_POST['delete-id']);
        $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '?c=post&a=index';

        $post = getPostById($id);

        $safeImage = basename($post['post_image']);
        $filename = 'assets/uploads/permanent/' . $safeImage;

        if (file_exists($filename)) {
            unlink($filename);
        }

        $deletePost = deletePost($id);

        if ($deletePost) {
            // if (preg_match('/^\?c=post&a=(index|all|userPosts)(&page=\d+)?$/', $redirect))
            if (preg_match('/^\?(c=post&a=(index|all)|c=profile&a=preview(&id=\d+)?)(&page=\d+)?$/', $redirect)) {
                header("Location: $redirect");
                exit;
            }
        }

    }
}


// ==================================== Remove Session + temp file if user select another picture ======================================

function post_clearTempFile()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['temp-upload'])) {

        $tempPath = $_SESSION['temp-upload']['file-temp-path'];

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
        unset($_SESSION['temp-upload']);
        echo json_encode(['status' => 'success']);
        exit;
    }
    echo json_encode(['status' => 'error']);
    exit;
}


// ======================= Helper Function for validation of Images =======================

function validateImage()
{

    if (!$_FILES['image'] || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        return ['error' => 'Select a Post Image'];
    }

    $file = $_FILES['image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Upload Error (' . $file['error'] . ')'];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS, true)) {
        return ['error' => 'Invalid file type. Allowed type : ' . implode(',', ALLOWED_EXTENSIONS)];
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['error' => 'Maximum File Size Allowed : ' . MAX_UPLOAD_SIZE];
    }

    return [
        'original-name' => $file['name'],
        'storage-name' => uniqid('img_', true) . '.' . $extension,
        'php-temp-dir' => $file['tmp_name'],
        'extension' => $extension,
    ];
}
