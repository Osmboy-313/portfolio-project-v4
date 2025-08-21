<?php 

require_once __DIR__ . '/../core/db.php';

function check_user_exists($detail = []){

    if (!isset($detail['column'], $detail['value'])) {
        return 'Missing column or value';
    }

    $column = $detail['column'];
    $value = $detail['value'];
    $allowed_columns = ['id', 'email', 'username'];

    if (!in_array($column, $allowed_columns)) {
        return 'Invalid column name';
    }

    $conn = db();
    $sql = $conn->prepare("SELECT * FROM `users` WHERE `$column` = ? ");
    $sql->bind_param('s', $value );
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_user_pass($email, $userType){
    $conn = db();

    $sql = $conn->prepare("SELECT * FROM `users` WHERE `email` = ? AND `user_type` = ? ");
    $sql->bind_param('ss', $email, $userType);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_assoc();
}

function addUser($username, $email, $userType, $password){
    $conn = db();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = $conn->prepare("INSERT INTO `users` (`username`, `email`, `user_type`, `password`) VALUES ( ? , ? , ? , ? ) ");
    $sql->bind_param('ssss', $username, $email, $userType, $hashedPassword);
    return $sql->execute();
    
}

function getUsers(){
    $conn = db();

    $userType = 'user';
    $sql = $conn->prepare("SELECT * FROM `users` WHERE `user_type` = ? ");
    $sql->bind_param('s', $userType);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAdmins(){
    $conn = db();

    $userType = 'admin';
    $sql = $conn->prepare("SELECT * FROM `users` WHERE `user_type` = ? ");
    $sql->bind_param('s', $userType);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getBosses(){
    $conn = db();

    $userType = 'boss';
    $sql = $conn->prepare("SELECT * FROM `users` WHERE `user_type` = ? ");
    $sql->bind_param('s', $userType);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}



function getAccountsPaginated($userType, $limit, $offset) {
    $conn = db();

    $sql = $conn->prepare("SELECT * FROM users WHERE user_type = ? LIMIT ? OFFSET ?");
    $sql->bind_param('sii', $userType, $limit, $offset);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAccountsCount($userType) {
    $conn = db();

    $sql = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE user_type = ?");
    $sql->bind_param('s', $userType);
    $sql->execute();
    $result = $sql->get_result()->fetch_assoc();

    return $result['total'];
}




function getUserWithPosts($id, $recordsPerPage, $offset){
    $conn = db();

    $userStmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $userStmt->bind_param('i', $id);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    $user = $userResult->fetch_assoc();

    if (!$user) return null;

    // Now, get the posts
    $postStmt = $conn->prepare("SELECT posts.*, categories.category_name AS category_name FROM posts JOIN categories ON posts.post_category = categories.id WHERE post_user = ? LIMIT ? OFFSET ? ");
    $postStmt->bind_param('iii', $id, $recordsPerPage,$offset);
    $postStmt->execute();
    $postResult = $postStmt->get_result();
    $posts = $postResult->fetch_all(MYSQLI_ASSOC);

    return [
        'user' => $user,
        'posts' => $posts
    ];
}

function getUserById($id){
    $conn = db();

    $sql = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $sql->bind_param('i', $id);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_assoc();
}

function updateUser($id, $fields) {
    $conn = db();

    if (empty($fields)) {
        return false;
    }

    $allowed = ['username', 'email', 'user_type', 'password', 'profile_picture'];
    $set = [];
    $values = [];

    foreach ($fields as $key => $value) {
        if (!in_array($key, $allowed)) continue;

        // Special handling for password
        if ($key === 'password') {
            $value = password_hash($value, PASSWORD_DEFAULT);
        }

        $set[] = "`$key` = ?";
        $values[] = $value;
    }

    if (empty($set)) return false;

    $sql = "UPDATE users SET " . implode(', ', $set) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);

    $types = str_repeat('s', count($values)) . 'i';
    $values[] = $id;

    $stmt->bind_param($types, ...$values);
    return $stmt->execute();
}

function deleteUser($id) {
    $conn = db();

    $sql = $conn->prepare("DELETE FROM `users` WHERE id = ?");
    $sql->bind_param('i', $id);
    

    return $sql->execute();
}

function doesUserExists($column, $value, $idToExclude = 0){

    $conn = db();
    $sql = $conn->prepare("SELECT * FROM `users` WHERE `$column` = ? AND id != ? ");
    $sql->bind_param('si', $value, $idToExclude);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}



?>